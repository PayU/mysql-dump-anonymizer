<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Entity\LineInfo;

interface LineParserInterface
{

    public function lineInfo(string $line) : LineInfo;

    /**
     * @param string $line
     * @return Value[][]  iterate the rows from the (multiple) insert line
     */
    public function getRowFromInsertLine(string $line) : iterable;

}
