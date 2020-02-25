<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\DatabaseValue;

class Url implements InterfaceDataType
{

    public function anonymize(DatabaseValue $value): DatabaseValue
    {
        return $value;
    }
}