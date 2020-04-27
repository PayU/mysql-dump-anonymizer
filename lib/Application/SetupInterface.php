<?php

namespace PayU\MysqlDumpAnonymizer\Application;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserInterface;
use PayU\MysqlDumpAnonymizer\WriteDump\LineDumpInterface;

interface SetupInterface
{
    public function setup(): void;

    public function getLineParser(): LineParserInterface;

    public function getAnonymizationProvider(): AnonymizationProviderInterface;

    public function getLineDump(): LineDumpInterface;
}
