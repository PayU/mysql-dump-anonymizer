<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\LineInfo;

interface InterfaceLineParser {

    public function lineInfo(string $line) : LineInfo;

    public function getRowFromInsertLine(string $line);

    /**
     * @param string $table
     * @param array $columns
     * @param AnonymizedValue[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $values) : string;

}