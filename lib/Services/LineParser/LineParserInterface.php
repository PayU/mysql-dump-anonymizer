<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\LineInfo;

interface LineParserInterface
{

    public function lineInfo(string $line) : LineInfo;

    /**
     * @param string $line
     * @return Value[][]  iterate the rows from the (multiple) insert line
     */
    public function getRowFromInsertLine(string $line) : iterable;

    /**
     * @param string $table
     * @param array $columns
     * @param AnonymizedValue[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $values) : string;
}
