<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\Value;

interface InterfaceDataType {
    public function anonymize(Value $value): Value;
}