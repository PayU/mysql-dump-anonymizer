<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizationActions;
use PayU\MysqlDumpAnonymizer\Services\ValueAnonymizerFactory;
use PayU\MysqlDumpAnonymizer\Provider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\LineParser\LineParserInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\NoAnonymization;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\ValueAnonymizerInterface;

class Anonymizer
{
    /** @var CommandLineParameters */
    private $commandLineParameters;

    /** @var Observer */
    private $observer;

    /**
     * @var Config
     */
    private $config;

    /** @var AnonymizationProviderInterface */
    private $anonymizationProvider;

    /** @var LineParserInterface */
    private $lineParser;

    public function __construct(
        CommandLineParameters $commandLineParameters,
        AnonymizationProviderInterface $anonymizationProvider,
        LineParserInterface $lineParser,
        Observer $observer,
        Config $config
    ) {
        $this->commandLineParameters = $commandLineParameters;
        $this->anonymizationProvider = $anonymizationProvider;
        $this->lineParser = $lineParser;
        $this->observer = $observer;
        $this->config = $config;
    }


    public function run($inputStream, $outputStream): void
    {

        $this->observer->notify(Observer::EVENT_BEGIN, $this->commandLineParameters->getEstimatedDumpSize());

        while ($line = $this->readLine($inputStream)) {
            fwrite($outputStream, $this->anonymizeLine($line));
            $this->observer->notify(Observer::EVENT_AFTER_LINE_PROCESSING);
        }

        $this->observer->notify(Observer::EVENT_END);
    }

    private function readLine($inputStream)
    {
        $this->observer->notify(Observer::EVENT_START_READ);
        $line = fgets($inputStream);
        $this->observer->notify(Observer::EVENT_END_READ, strlen(is_string($line)?$line:''));
        return $line;
    }


    private function anonymizeLine($line): string
    {
        $lineInfo = $this->lineParser->lineInfo($line);
        if ($lineInfo->isInsert() === false) {
            $this->observer->notify(Observer::EVENT_NOT_AN_INSERT);
            return $line;
        }

        $table = $lineInfo->getTable();

        //truncate action doesnt write inserts
        if ($this->anonymizationProvider->getTableAction($table) === AnonymizationActions::TRUNCATE) {
            $this->observer->notify(Observer::EVENT_TRUNCATE);
            return '';
        }

        if ($lineInfo->isInsert() === false) {
            $this->observer->notify(Observer::EVENT_NOT_AN_INSERT);
            return $line;
        }

        $lineColumns = $lineInfo->getColumns();


        $insertRequiresAnonymization = false;
        foreach ($lineColumns as $column) {
            $valueAnonymizer = $this->anonymizationProvider->getAnonymizationFor($table, $column);
            if (get_class($valueAnonymizer) !== ValueAnonymizerFactory::getValueAnonymizers()[ValueAnonymizerFactory::NO_ANONYMIZATION]) {
                $insertRequiresAnonymization = true;
                break;
            }
        }

        //When insert line doesnt have anything to anonymize, return it as-is
        if ($insertRequiresAnonymization === false) {
            $this->observer->notify(Observer::EVENT_INSERT_LINE_NO_ANONYMIZATION);
            return $line;
        }

        //we have at least one column to anonymize

        $anonymizedValues = [];
        foreach ($this->lineParser->getRowFromInsertLine($line) as $row) {
            $anonymizedValue = [];
            /** @var Value[] $row */
            foreach ($row as $columnIndex => $cell) {
                $anonymizedValue[] = $this->anonymizeValue(
                    $this->anonymizationProvider->getAnonymizationFor($table, $lineColumns[$columnIndex]),
                    $cell,
                    array_combine($lineColumns, $row)
                );
            }


            $anonymizedValues[] = $anonymizedValue;
        }

        return $this->lineParser->rebuildInsertLine($table, $lineColumns, $anonymizedValues);
    }

    /**
     * @param ValueAnonymizerInterface $valueAnonymizer
     * @param Value $value
     * @param Value[] $row Associative array columnName => Value Object
     * @return AnonymizedValue
     */
    private function anonymizeValue(ValueAnonymizerInterface $valueAnonymizer, Value $value, $row): AnonymizedValue
    {
        if ($value->isExpression() && $value->getRawValue() === 'NULL') {
            $this->observer->notify(Observer::EVENT_NULL_VALUE, get_class($valueAnonymizer));
            return new AnonymizedValue('NULL');
        }

        if ($valueAnonymizer instanceof NoAnonymization) {
            $this->observer->notify(Observer::EVENT_NO_ANONYMIZATION);
        }

        $this->observer->notify(Observer::EVENT_ANONYMIZATION_START, get_class($valueAnonymizer));
        $ret = $valueAnonymizer->anonymize($value, $row, $this->config);
        $this->observer->notify(Observer::EVENT_ANONYMIZATION_END, get_class($valueAnonymizer));
        return $ret;
    }
}
