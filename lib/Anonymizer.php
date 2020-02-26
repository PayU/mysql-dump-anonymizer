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
        RuntimeProgress::$output = $errorStream;
        while ($line = fgets($inputStream)) {
            $readSoFar += strlen($line);
            RuntimeProgress::show($readSoFar, $total);
            fwrite($outputStream, $this->anonymizeLine($line, $config, $lineParser));
        }
    }



 private function anonymizeLine($line, AnonymizationConfig $config, InterfaceLineParser $lineParser)
    {
        $lineInfo = $lineParser->lineInfo($line);
        if ($lineInfo->isInsert() === false) {
            return $line;
        }

        //truncate action doesnt write inserts
        if ($config->getActionConfig($lineInfo->getTable())->getAction() === AnonymizationActions::TRUNCATE) {
            //TODO make fwrite not write '' when no need to write
            return '';
        }
        $lineColumns = $lineInfo->getColumns();
        $configColumns = $config->getActionConfig($lineInfo->getTable())->getColumns();

        //Check if config contains all
        if (count($lineColumns) !== count($configColumns)) {
            throw new RuntimeException('Number of columns in table ' . $lineInfo->getTable() . ' doesnt match config.');
        }

        foreach ($lineColumns as $lineColumn) {
            if (!array_key_exists($lineColumn, $configColumns)) {
                throw new RuntimeException('Column not found in config ' . $lineColumn . '');
            }
        }

        //no insert or there is not anonymization required for any of the columns
        if ($lineInfo->isInsert() === false || empty(array_filter($configColumns))) {
            return $line;
        }

        //we have at least one column to anonymize
        $dumpQuery = 'INSERT'.' INTO '.$lineInfo->getTable().' (`';
        $dumpQuery .= implode('`, `', $lineColumns );
        $dumpQuery .= '`) VALUES ';
        foreach ($lineParser->getRowFromInsertLine($line) as $row) {
            /** @var Value[] $row */
            $dumpQuery .= '(';
            foreach ($row as $columnIndex => $cell) {
                $columnName = $lineColumns[$columnIndex];
                $dumpQuery .= $this->anonymizeValue($configColumns[$columnName], $cell, array_combine($lineColumns, $row))->getQuotedValue();
                $dumpQuery .= ', ';
            }
            $dumpQuery = substr($dumpQuery, 0, -2);
            $dumpQuery .= '),';
        }
        return substr($dumpQuery, 0, -1).";\n";
    }

    private function anonymizeValue(AnonymizationColumnConfig $columnConfig, Value $value, $row)
    {

        if ($dataType = $this->dataTypeService->getDataType($columnConfig, $row)) {
            $databaseValue = $this->dataTypeService->anonymizeValue($value, $dataType);

            if ($databaseValue->isExpression()) {
                $value->setQuotedValue($databaseValue->getValue());
                return $value;
            }

            $value->setQuotedValue(
                '\'' . addcslashes($databaseValue->getValue(), "'\\\n") . '\''
            );
            return $value;
        }
        return $value;
    }

}
