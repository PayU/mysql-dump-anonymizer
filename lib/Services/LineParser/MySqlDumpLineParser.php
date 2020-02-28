<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;
use Generator;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use RuntimeException;

//TODO all-in-one
class MySqlDumpLineParser implements InterfaceLineParser
{

    public const INSERT_START_STRING = 'INSERT INTO';
    public const INSERT_START_LENGTH = 11; //strlen(above)

    private const MARK_1 = '` (`';
    private const MARK_1_LEN = 4;
    private const MARK_2 = '`) VALUES (';
    private const COL_DELIM = '`, `';

    private const INSERT_LINE_PATTERN = '#^INSERT\s+INTO\s+`([^`]+)`\s*\(((?:`[^`]+`(?:\s*,\s*)?)+)\)\s+VALUES\s*(.*)\s*;$#';


    /**
     * @param string $line
     * @return LineInfo
     */
    public function lineInfo(string $line): LineInfo
    {
        $isInsert = (strpos($line, self::INSERT_START_STRING) === 0);

        $table = null;
        $columns = null;

        if ($isInsert) {
            $first = strpos($line, '`', 0) + 1;
            $table = substr($line, $first, strpos($line, '`', $first) - $first);

            $mark = strpos($line, self::MARK_1, self::INSERT_START_LENGTH);
            $markLastPost = $mark + self::MARK_1_LEN;
            $columnsString = substr($line, $markLastPost, strpos($line, self::MARK_2, $markLastPost) - $markLastPost);
            $columns = explode(self::COL_DELIM, $columnsString);
        }

        return new LineInfo($isInsert, $table, $columns);
    }


    /**
     * @param string $line
     * @return Value[][]
     */
    public function getRowFromInsertLine(string $line) : iterable
    {
        yield from $this->parseValuesList($line);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param Value[][] $rows
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $rows) : string
    {
        $dumpQuery = 'INSERT'.' INTO `'.$table.'` (`';
        $dumpQuery .= implode('`, `', $columns );
        $dumpQuery .= '`) VALUES (';

        foreach ($rows as $row) {
            foreach ($row as $value) {
                $dumpQuery .= $value->getRawValue().', ';
            }
            $dumpQuery = substr($dumpQuery,0 , -2).'), (';
        }
        return substr($dumpQuery,0 , -3).';';
    }

    /**
     * @param string $insertLineString
     * @return Value[][]
     * @noinspection PhpDocSignatureInspection
     */
    private function parseValuesList(string $insertLineString): Generator
    {
        if (!preg_match(self::INSERT_LINE_PATTERN, $insertLineString, $match)) {
            throw new RuntimeException('Invalid insert line');
        }

        $rawValues = $match[3];

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
                        yield $row;
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
                            $row[] = new Value($rawValue, $this->unEscape($rawValue), $this->isExpression($rawValue));
                            break;
                        }
                        if ($char === ')') {
                            $parseLevel = 0;
                            $row[] = new Value($rawValue, $this->unEscape($rawValue), $this->isExpression($rawValue));
                            yield $row;
                            break;
                        }
                        $rawValue .= $char;
                    } else {
                        if ($char === $valueEscaping) {
                            $parseLevel = 1;
                            $rawValue .= $char;
                            $row[] = new Value($rawValue, $this->unEscape($rawValue), $this->isExpression($rawValue));
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
    }

    private function isExpression($rawValue) : bool {
        return (false === (strpos($rawValue, '\'') === 0 && substr($rawValue, -1) === '\''));
    }

    private function unEscape($rawValue): string
    {
        if ($this->isExpression($rawValue)) {
            return $rawValue;
        }

        $replaced = [
            "\\r" => "\r",
            "\\n" => "\n",
            "\\t" => "\t"
        ];
        $unEscapedValue = str_replace(array_keys($replaced), $replaced, $rawValue);
        return stripslashes(substr($unEscapedValue, 1, -1));

    }


}