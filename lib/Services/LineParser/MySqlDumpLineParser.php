<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;
use Generator;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Exceptions\InsertLineParserException;
use PayU\MysqlDumpAnonymizer\Parser\InsertLineStringParser;

class MySqlDumpLineParser implements InterfaceLineParser
{

    public const INSERT_START_STRING = 'INSERT INTO';
    public const INSERT_START_LENGTH = 11; //strlen(above)

    private const MARK_1 = '` (`';
    private const MARK_1_LEN = 4;
    private const MARK_2 = '`) VALUES (';
    private const COL_DELIM = '`, `';

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
     * @throws InsertLineParserException
     * @noinspection PhpDocSignatureInspection
     */
    public function getRowFromInsertLine(string $line) : Generator
    {
        $insertLine = (new InsertLineStringParser())->parse($line);
        yield from $insertLine->getValuesList();
    }

    /**
     * @param string $table
     * @param array $columns
     * @param Value[][] $rows
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, $rows) : string
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


}