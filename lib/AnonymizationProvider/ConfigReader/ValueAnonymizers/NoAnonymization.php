<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\ReadDump\Value;

final class NoAnonymization implements ValueAnonymizerInterface
{

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        return new AnonymizedValue($value->getRawValue());
    }
}
