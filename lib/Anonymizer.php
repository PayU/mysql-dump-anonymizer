<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Dumper\InsertLineMysqlLikeDumper;
use PayU\MysqlDumpAnonymizer\Parser\InsertLineStringParser;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\ValueAnonymizerRegistry;

class Anonymizer
{
    /** @var InsertLineStringParser */
    private $insertLineParser;
    /** @var InsertLineMysqlLikeDumper */
    private $insertLineDumper;
    /** @var ValueAnonymizerRegistry */
    private $valueAnonymizerRegistry;

    private const INSERT_LINE_PATTERN = '#^INSERT\s+INTO\s+#';

    public function __construct(
        InsertLineParser $insertLineParser,
        InsertLineDumper $insertLineDumper,
        ValueAnonymizerRegistry $valueAnonymizerRegistry
    ) {
        $this->insertLineParser = $insertLineParser;
        $this->insertLineDumper = $insertLineDumper;
        $this->valueAnonymizerRegistry = $valueAnonymizerRegistry;
    }


    public function anonymize($inputStream, $outputStream): void
    {
        while (false !== ($line = fgets($inputStream))) {
            fwrite($outputStream, $this->anonymizeLine($line));
        }
    }

    private function anonymizeLine(string $line): string
    {
        if (!preg_match(self::INSERT_LINE_PATTERN, $line, $match)) {
            return $line;
        }

        try {
            $insertLine = $this->insertLineParser->parse($line);
        } catch (InsertLineParserException $ignoreLine) {
            return $line;
        }

        $anonymizedInsertLine = $this->anonymizeInsertLine($insertLine);

        return $this->insertLineDumper->dump($anonymizedInsertLine);
    }

    private function anonymizeValue($table, $column, Value $value): Value
    {
        $valueAnonymizer = $this->valueAnonymizerRegistry->getAnonymizer($table, $column);

        return $valueAnonymizer->anonymize($value);
    }

    /**
     * @param InsertLine $insertLine
     * @return InsertLine
     */
    private function anonymizeInsertLine(InsertLine $insertLine): InsertLine
    {
        $table = $insertLine->getTable();
        $columns = $insertLine->getColumns();
        $valuesList = $insertLine->getValuesList();

        $anonymizedValuesList = [];
        foreach ($valuesList as $values) {
            $anonymizedValues = [];
            foreach ($values as $columnIndex => $value) {
                $anonymizedValue = $this->anonymizeValue($table, $columns[$columnIndex], $value);
                $anonymizedValues[$columnIndex] = $anonymizedValue;
            }
            $anonymizedValuesList[] = $anonymizedValues;
        }

        return new InsertLine($table, $columns, $anonymizedValuesList);
    }

}
