<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

//TODO rename to ValueAnonymizer
interface InterfaceDataType {
    /**
     * @param Value $value
     * @return AnonymizedValue
     */
    public function anonymize(Value $value): AnonymizedValue;
}