<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;


interface ValueAnonymizerInterface
{
    /**
     * @param Value $value
     * @param array $row
     * @param ConfigInterface $config
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row, ConfigInterface $config): AnonymizedValue;
}
