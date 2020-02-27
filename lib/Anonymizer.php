<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationActions;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationColumnConfig;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationConfig;
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

    public function __construct(
        CommandLineParameters $commandLineParameters,
        ConfigFactory $configFactory,
        LineParserFactory $lineParserFactory,
        DataTypeService $dataTypeService
    )
    {
        $this->commandLineParameters = $commandLineParameters;
        $this->configFactory = $configFactory;
        $this->lineParserFactory = $lineParserFactory;
        $this->dataTypeService = $dataTypeService;
    }



    public function run($inputStream, $outputStream, $errorStream): void
    {
        try {

            $this->commandLineParameters->setCommandLineArguments($_SERVER['argv']);
            $this->commandLineParameters->validate();

            $configService = $this->configFactory->make(
                $this->commandLineParameters->getConfigType(),
                $this->commandLineParameters->getConfigFile()
            );

            $configService->validate();

            $config = $configService->buildConfig();

        } catch (InvalidArgumentException | ConfigValidationException $e) {
            fwrite($errorStream, 'ERROR: ' . $e->getMessage() . "\n");
            fwrite($errorStream, $this->commandLineParameters->help());
            exit(1);
        }

        $lineParser = $this->lineParserFactory->chooseLineParser($this->commandLineParameters->getLineParser());

        $total = $this->commandLineParameters->getEstimatedDumpSize();
        $readSoFar = 0;
        if ($this->commandLineParameters->isShowProgress()) {
            RuntimeProgress::$output = $errorStream;
            RuntimeProgress::output(PHP_EOL . PHP_EOL);
        }

        $startRead = microtime(true);
        RuntimeProgress::$start = $startRead;

        while ($line = fgets($inputStream)) {
            RuntimeProgress::$readTime += (microtime(true) - $startRead);
            $readSoFar += strlen($line);

            $string = $this->anonymizeLine($line, $config, $lineParser);

            $startWrite = microtime(true);
            fwrite($outputStream, $string);
            RuntimeProgress::$writeTime += (microtime(true) - $startWrite);

            if ($this->commandLineParameters->isShowProgress()) {
                RuntimeProgress::show($readSoFar, $total);
            }


            $startRead = microtime(true);
        }
    }



 private function anonymizeLine($line, AnonymizationConfig $config, InterfaceLineParser $lineParser)
    {
        $lineInfo = $lineParser->lineInfo($line);
        if ($lineInfo->isInsert() === false) {
            if (!array_key_exists('NOT-INSERT', RuntimeProgress::$anonymizationTimeDataTypes)) {
                RuntimeProgress::$anonymizationTimeDataTypes['NOT-INSERT'] = 0;
                RuntimeProgress::$anonymizationTimeDataTypesCount['NOT-INSERT'] = 0;
            }
            RuntimeProgress::$anonymizationTimeDataTypesCount['NOT-INSERT']++;
            return $line;
        }

        $table = $lineInfo->getTable();

        //truncate action doesnt write inserts
        if ($config->getActionConfig($table)->getAction() === AnonymizationActions::TRUNCATE) {
            //TODO make fwrite not write '' when no need to write
            if (!array_key_exists('TRUNCATE', RuntimeProgress::$anonymizationTimeDataTypes)) {
                RuntimeProgress::$anonymizationTimeDataTypes['TRUNCATE'] = 0;
                RuntimeProgress::$anonymizationTimeDataTypesCount['TRUNCATE'] = 0;
            }
            RuntimeProgress::$anonymizationTimeDataTypesCount['TRUNCATE']++;

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
                throw new RuntimeException('Column not found in config ' . $table.' '.$lineColumn);
            }
        }

        //no insert or there is not anonymization required for any of the columns
        if ($lineInfo->isInsert() === false || empty(array_filter($configColumns))) {
            if (!array_key_exists('NO-ANON', RuntimeProgress::$anonymizationTimeDataTypes)) {
                RuntimeProgress::$anonymizationTimeDataTypes['NO-ANON-LINE'] = 0;
                RuntimeProgress::$anonymizationTimeDataTypesCount['NO-ANON-LINE'] = 0;
            }
            RuntimeProgress::$anonymizationTimeDataTypesCount['NO-ANON-LINE']++;

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
     * @return Value
     */
    private function anonymizeValue(AnonymizationColumnConfig $columnConfig, Value $value, $row)
    {

        if ($dataType = $this->dataTypeService->getDataType($columnConfig, $row)) {

            /** @noinspection GetClassUsageInspection - no null here ffs*/
            $gc = get_class($dataType);
            $a = explode("\\", $gc);
            $stringDataType = array_pop($a);

            //TODO refactor rimt
            if (!array_key_exists($stringDataType, RuntimeProgress::$anonymizationTimeDataTypes)) {
                RuntimeProgress::$anonymizationTimeDataTypes[$stringDataType] = 0;
                RuntimeProgress::$anonymizationTimeDataTypesCount[$stringDataType] = 0;
            }

            // NULL values will not go trough anonymization
            if ($value->isExpression() && $value->getRawValue() === 'NULL') {
                if (!array_key_exists('NULL', RuntimeProgress::$anonymizationTimeDataTypes)) {
                    RuntimeProgress::$anonymizationTimeDataTypes['NULL'] = 0;
                    RuntimeProgress::$anonymizationTimeDataTypesCount['NULL'] = 0;
                }
                RuntimeProgress::$anonymizationTimeDataTypesCount['NULL']++;
                return $value;
            }

            $startAnon = microtime(true);
            $value = $dataType->anonymize($value);
            $anonTime = microtime(true) - $startAnon;
            RuntimeProgress::$anonymizationTime += $anonTime;


            RuntimeProgress::$anonymizationTimeDataTypes[$stringDataType] += $anonTime;
            RuntimeProgress::$anonymizationTimeDataTypesCount[$stringDataType]++;

        }

        if (!array_key_exists('NO-ANON', RuntimeProgress::$anonymizationTimeDataTypes)) {
            RuntimeProgress::$anonymizationTimeDataTypes['NO-ANON'] = 0;
            RuntimeProgress::$anonymizationTimeDataTypesCount['NO-ANON'] = 0;
        }
        RuntimeProgress::$anonymizationTimeDataTypesCount['NO-ANON']++;


        return $value;
    }

}
