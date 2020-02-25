<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\DatabaseValue;

interface InterfaceDataType {
    public function anonymize(DatabaseValue $value): DatabaseValue;
}