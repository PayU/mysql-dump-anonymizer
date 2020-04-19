<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Entity;

//TODO find a better place
interface ValueAnonymizerInterface
{
    /**
     * @param Value $value
     * @param array $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue;
}
