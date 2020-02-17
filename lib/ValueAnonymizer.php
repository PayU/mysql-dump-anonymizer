<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

interface ValueAnonymizer
{

    public function anonymize(Value $value): Value;
}
