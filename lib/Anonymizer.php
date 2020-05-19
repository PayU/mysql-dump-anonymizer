<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Application\Observer;
use PayU\MysqlDumpAnonymizer\Application\ObserverInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationAction;
use PayU\MysqlDumpAnonymizer\WriteDump\LineDumpInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;

class Anonymizer
{
    private ObserverInterface $observer;
    private AnonymizationProviderInterface $anonymizationProvider;
    private LineParserInterface $lineParser;
    private LineDumpInterface $lineDump;

    public function __construct(
        AnonymizationProviderInterface $anonymizationProvider,
        LineParserInterface $lineParser,
        LineDumpInterface $lineDump,
        ObserverInterface $observer
    ) {
        $this->anonymizationProvider = $anonymizationProvider;
        $this->lineParser = $lineParser;
        $this->lineDump = $lineDump;
        $this->observer = $observer;
    }


    public function run($inputStream, $outputStream): void
    {

        while ($line = $this->readLine($inputStream)) {
            fwrite($outputStream, $this->anonymizeLine($line));
            $this->observer->notify(Observer::EVENT_AFTER_LINE_PROCESSING, null);
        }

        $this->observer->notify(Observer::EVENT_END, null);
    }

    private function readLine($inputStream)
    {
        $this->observer->notify(Observer::EVENT_START_READ, null);
        $line = fgets($inputStream);
        $this->observer->notify(Observer::EVENT_END_READ, strlen(is_string($line) ? $line : ''));
        return $line;
    }


    private function anonymizeLine($line): string
    {
        $lineInfo = $this->lineParser->lineInfo($line);
        if ($lineInfo->isInsert() === false) {
            $this->observer->notify(Observer::EVENT_NOT_AN_INSERT, null);
            return $line;
        }

        $table = $lineInfo->getTable();

        //truncate action doesnt write inserts
        if ($this->anonymizationProvider->getTableAction($table) === AnonymizationAction::TRUNCATE) {
            $this->observer->notify(Observer::EVENT_TRUNCATE, null);
            return '';
        }

        if ($lineInfo->isInsert() === false) {
            $this->observer->notify(Observer::EVENT_NOT_AN_INSERT, null);
            return $line;
        }

        $lineColumns = $lineInfo->getColumns();

        $valueAnonymizers = [];
        $insertRequiresAnonymization = false;
        foreach ($lineColumns as $columnIndex => $column) {
            $valueAnonymizers[$columnIndex] = $this->anonymizationProvider->getAnonymizationFor($table, $column);
            if ($this->anonymizationProvider->isAnonymization($valueAnonymizers[$columnIndex])) {
                $insertRequiresAnonymization = true;
            }
        }

        //When insert line doesnt have anything to anonymize, return it as-is
        if ($insertRequiresAnonymization === false) {
            $this->observer->notify(Observer::EVENT_INSERT_LINE_NO_ANONYMIZATION, null);
            return $line;
        }

        //we have at least one column to anonymize

        $anonymizedValues = [];
        foreach ($lineInfo->getValuesParser() as $row) {
            $anonymizedValue = [];
            /** @var Value[] $row */
            foreach ($row as $columnIndex => $cell) {
                $anonymizedValue[] = $this->anonymizeValue(
                    $valueAnonymizers[$columnIndex],
                    $cell,
                    array_combine($lineColumns, $row)
                );
            }


            $anonymizedValues[] = $anonymizedValue;
        }

        return $this->lineDump->rebuildInsertLine($table, $lineColumns, $anonymizedValues);
    }

    /**
     * @param ValueAnonymizerInterface $valueAnonymizer
     * @param Value $value
     * @param Value[] $row Associative array columnName => Value Object
     * @return AnonymizedValue
     */
    private function anonymizeValue(ValueAnonymizerInterface $valueAnonymizer, Value $value, $row): AnonymizedValue
    {
        if ($value->getRawValue() === 'NULL') {
            $this->observer->notify(Observer::EVENT_NULL_VALUE, get_class($valueAnonymizer));
            return AnonymizedValue::fromRawValue('NULL');
        }

        if ($value->getRawValue() === '\'\'') {
            return AnonymizedValue::fromRawValue('\'\'');
        }

        if ($this->anonymizationProvider->isAnonymization($valueAnonymizer) === false) {
            $this->observer->notify(Observer::EVENT_NO_ANONYMIZATION, null);
        }
        $this->observer->notify(Observer::EVENT_ANONYMIZATION_START, get_class($valueAnonymizer));
        $ret = $valueAnonymizer->anonymize($value, $row);
        $this->observer->notify(Observer::EVENT_ANONYMIZATION_END, get_class($valueAnonymizer));
        return $ret;
    }
}
