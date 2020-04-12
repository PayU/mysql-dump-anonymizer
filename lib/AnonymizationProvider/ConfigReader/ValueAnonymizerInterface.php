<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader;


use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\ReadDump\Value;


interface ValueAnonymizerInterface
{
    /**
     * @param \PayU\MysqlDumpAnonymizer\ReadDump\Value $value
     * @param array $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValueInterface;
}
