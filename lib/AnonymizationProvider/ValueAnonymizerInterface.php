<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

interface ValueAnonymizerInterface
{
    /**
     * @param Value $value
     * @param array $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue;
}
