<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;

interface LineParserInterface
{
    public function lineInfo(string $line) : LineInfo;
}
