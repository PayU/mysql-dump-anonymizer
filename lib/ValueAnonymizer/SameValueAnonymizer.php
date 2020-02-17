<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer;

class SameValueAnonymizer implements ValueAnonymizer
{

    public function anonymize(Value $value): Value
    {
        return clone $value;
    }
}
