<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationActions;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationColumnConfig;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationConfig;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use PayU\MysqlDumpAnonymizer\Services\ConfigFactory;
use PayU\MysqlDumpAnonymizer\Services\DataTypeService;
use PayU\MysqlDumpAnonymizer\Services\LineParser\InterfaceLineParser;
use PayU\MysqlDumpAnonymizer\Services\LineParserFactory;
use RuntimeException;

class Anonymizer
{
    /** @var CommandLineParameters */
    private $commandLineParameters;

    /** @var LineParserFactory */
    private $lineParserFactory;

    /** @var ConfigFactory */
    private $configFactory;

    /** @var DataTypeService */
    private $dataTypeService;

    /** @var Observer */
    private $observer;

    public function __construct(
        CommandLineParameters $commandLineParameters,
        ConfigFactory $configFactory,
        LineParserFactory $lineParserFactory,
        DataTypeService $dataTypeService,
        Observer $observer
    )
    {
        $this->commandLineParameters = $commandLineParameters;
        $this->configFactory = $configFactory;
        $this->lineParserFactory = $lineParserFactory;
        $this->dataTypeService = $dataTypeService;
        $this->observer = $observer;
    }


    public function run($inputStream, $outputStream, $errorStream): void
    {
        try {
            $configService = $this->configFactory->make(
                $this->commandLineParameters->getConfigType(),
                $this->commandLineParameters->getConfigFile()
            );

            $configService->validate();

            $config = $configService->buildConfig();

        } catch (InvalidArgumentException | ConfigValidationException $e) {
            fwrite($errorStream, 'ERROR: ' . $e->getMessage() . "\n");
            fwrite($errorStream, CommandLineParameters::help());
            exit(1);
        }

        $lineParser = $this->lineParserFactory->chooseLineParser($this->commandLineParameters->getLineParser());

        $this->observer->notify(Observer::EVENT_BEGIN, $this->commandLineParameters->getEstimatedDumpSize());

        while ($line = $this->readLine($inputStream)) {
            fwrite($outputStream, $this->anonymizeLine($line, $config, $lineParser));
            $this->observer->notify(Observer::EVENT_AFTER_LINE_PROCESSING);
        }
    }

    private function readLine($inputStream)
    {
        $this->observer->notify(Observer::EVENT_START_READ);
        $line = fgets($inputStream);
        $this->observer->notify(Observer::EVENT_END_READ, strlen(is_string($line)?$line:''));
        return $line;
    }


    private function anonymizeLine($line, AnonymizationConfig $config, InterfaceLineParser $lineParser)
    {
        $lineInfo = $lineParser->lineInfo($line);
        if ($lineInfo->isInsert() === false) {
            $this->observer->notify(Observer::EVENT_NOT_AN_INSERT);
            return $line;
        }

        $table = $lineInfo->getTable();

        //truncate action doesnt write inserts
        if ($config->getActionConfig($table)->getAction() === AnonymizationActions::TRUNCATE) {
            $this->observer->notify(Observer::EVENT_TRUNCATE);
            return '';
        }

        $lineColumns = $lineInfo->getColumns();
        $configColumns = $config->getActionConfig($table)->getColumns();

        //Check if config contains all
        if (count($lineColumns) !== count($configColumns)) {
            throw new RuntimeException('Number of columns in table ' . $table . ' doesnt match config.');
        }

        foreach ($lineColumns as $lineColumn) {
            if (!array_key_exists($lineColumn, $configColumns)) {
                throw new RuntimeException('Column not found in config ' . $table . ' ' . $lineColumn);
            }
        }

        //no insert or there is not anonymization required for any of the columns
        if ($lineInfo->isInsert() === false || empty(array_filter($configColumns))) {
            $this->observer->notify(Observer::EVENT_INSERT_LINE_NO_ANONYMIZATION);
            return $line;
        }

        //we have at least one column to anonymize

        $anonymizedValues = [];
        foreach ($lineParser->getRowFromInsertLine($line) as $row) {

            $anonymizedValue = [];
            /** @var Value[] $row */
            foreach ($row as $columnIndex => $cell) {
                $columnName = $lineColumns[$columnIndex];
                $anonymizedValue[] = $this->anonymizeValue($configColumns[$columnName], $cell, array_combine($lineColumns, $row));
            }
            $anonymizedValues[] = $anonymizedValue;
        }

        return $lineParser->rebuildInsertLine($table, $lineColumns, $anonymizedValues);
    }

    /**
     * @param AnonymizationColumnConfig $columnConfig
     * @param Value $value
     * @param Value[] $row Associative array columnName => Value Object
     * @return AnonymizedValue
     */
    private function anonymizeValue(AnonymizationColumnConfig $columnConfig, Value $value, $row): AnonymizedValue
    {

        if ($dataTypeString = $this->dataTypeService->getDataType($columnConfig, $row)) {

            $dataType = $this->dataTypeService->getDataTypeClass($dataTypeString);

            // NULL values will not go trough anonymization
            if ($value->isExpression() && $value->getRawValue() === 'NULL') {
                $this->observer->notify(Observer::EVENT_NO_ANONYMIZATION);
                $this->observer->notify(Observer::EVENT_NULL_VALUE, $dataTypeString);

                return new AnonymizedValue($value->getRawValue());
            }

            $this->observer->notify(Observer::EVENT_ANONYMIZATION_START, $dataTypeString);

            $anonymizedValue = $dataType->anonymize($value);

            $this->observer->notify(Observer::EVENT_ANONYMIZATION_END, $dataTypeString);

            return $anonymizedValue;
        }

        $this->observer->notify(Observer::EVENT_NO_ANONYMIZATION);

        return new AnonymizedValue($value->getRawValue());
    }

}
