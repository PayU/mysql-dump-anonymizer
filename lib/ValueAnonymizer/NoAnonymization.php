<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

class NoAnonymization implements ValueAnonymizerInterface
{

    public function anonymize(Value $value, array $row, ConfigInterface $config): AnonymizedValue
    {
        return new AnonymizedValue($value->getRawValue());
    }
}
