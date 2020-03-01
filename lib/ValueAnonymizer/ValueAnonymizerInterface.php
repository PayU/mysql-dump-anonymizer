<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;

interface ValueAnonymizerInterface
{
    /**
     * @param Value $value
     * @param array $row
     * @param Config $config
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue;
}
