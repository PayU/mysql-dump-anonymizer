<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;

interface InterfaceLineParser {

    public function lineInfo(string $line) : LineInfo;

    public function getRowFromInsertLine($line);

}