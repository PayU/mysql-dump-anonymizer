<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class NoAnonymization implements ValueAnonymizerInterface
{

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        return new AnonymizedValue($value->getRawValue());
    }
}
