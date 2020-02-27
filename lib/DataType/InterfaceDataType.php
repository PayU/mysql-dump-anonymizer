<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\Value;

//TODO rename to ValueAnonymizer
interface InterfaceDataType {
    /**
     * @param Value $value
     * @param array<string, Value> $row
     * @return Value
     * TODO add $row here?
     */
    public function anonymize(Value $value): Value;
}