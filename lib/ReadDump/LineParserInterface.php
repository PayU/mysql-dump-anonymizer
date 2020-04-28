<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ReadDump;

use PayU\MysqlDumpAnonymizer\ReadDump\LineInfo;

interface LineParserInterface
{
    public function lineInfo(string $line) : LineInfo;
}
