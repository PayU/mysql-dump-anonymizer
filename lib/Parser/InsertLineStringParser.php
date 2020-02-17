<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Parser;

use PayU\MysqlDumpAnonymizer\InsertLine;
use PayU\MysqlDumpAnonymizer\InsertLineParser;
use PayU\MysqlDumpAnonymizer\InsertLineParserException;
use PayU\MysqlDumpAnonymizer\Value;
use RuntimeException;

class InsertLineStringParser implements InsertLineParser
{
    private const INSERT_LINE_PATTERN = '#^INSERT\s+INTO\s+`([^`]+)`\s*\(((?:`[^`]+`(?:\s*,\s*)?)+)\)\s+VALUES\s*(.*?)\s*;$#';

    /**
     * @param string $line
     * @return InsertLine
     * @throws InsertLineParserException
     */
    public function parse(string $line): InsertLine
    {
        if (!preg_match(self::INSERT_LINE_PATTERN, $line, $match)) {
            throw new InsertLineParserException('Invalid insert line');
        }


        $table = $match[1];

        $columns = $this->parseColumns($match[2]);
        $valuesList = $this->parseValuesList($match[3]);

        return new InsertLine($table, $columns, $valuesList);
    }


    /**
     * @param string $rawColumns
     * @return string[]
     */
    private function parseColumns(string $rawColumns): array
    {
        return array_map(
            static function ($rawColumnName) {
                return trim($rawColumnName, '`');
            },
            preg_split('#,\s*#', $rawColumns)
        );
    }

    /**
     * @param string $rawValues
     * @return Value[][]
     */
    private function parseValuesList(string $rawValues): array
    {
        $rows = [];
        $row = [];
        $rawValue = '';

        $parseLevel = 0; // 0 - outside row, 1 - inside row but outside value, 2 - inside value
        $valueEscaping = null;
        $index = 0;
        $length = strlen($rawValues);

        while ($index < $length) {
            $char = $rawValues[$index];
            switch ($parseLevel) {
                case 0:
                    if (in_array($char, [' ', ','], true)) {
                        break;
                    }
                    if ($char === '(') {
                        $parseLevel = 1;
                        $row = [];
                        break;
                    }
                    throw new RuntimeException('Encountered in level 0 char: ' . $char);
                case 1:
                    if (in_array($char, [' ', ','], true)) {
                        break;
                    }
                    if ($char === ')') {
                        $parseLevel = 0;
                        $rows[] = $row;
                        break;
                    }

                    $parseLevel = 2;
                    $rawValue = $char;
                    if ($char === '\'') {
                        $valueEscaping = $char;
                    } else {
                        $valueEscaping = '';
                    }
                    break;
                case 2:
                    if ($valueEscaping === '') {
                        if (in_array($char, [' ', ','], true)) {
                            $parseLevel = 1;
                            $row[] = new Value($rawValue, '');
                            break;
                        }
                        if ($char === ')') {
                            $parseLevel = 0;
                            $row[] = new Value($rawValue, '');
                            $rows[] = $row;
                            break;
                        }
                        $rawValue .= $char;
                    } else {
                        if ($char === $valueEscaping) {
                            $parseLevel = 1;
                            $rawValue .= $char;
                            $row[] = new Value($rawValue, '');
                            break;
                        }
                        if ($char === '\\') {
                            $rawValue .= $char;
                            $index++;
                            $char = $rawValues[$index];
                        }
                        $rawValue .= $char;
                    }
            }
            $index++;
        }
        return $rows;
    }

}
