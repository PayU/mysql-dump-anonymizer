<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;
use PayU\MysqlDumpAnonymizer\Entity\Value;

interface InterfaceLineParser {

    public function lineInfo(string $line) : LineInfo;

    public function getRowFromInsertLine(string $line);

    /**
     * @param string $table
     * @param array $columns
     * @param Value[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, $values);

}