<?php

namespace PayU\MysqlDumpAnonymizer\DataType;



use PayU\MysqlDumpAnonymizer\Entity\Value;

class Url implements InterfaceDataType
{

    public function anonymize(Value $value): Value
    {
        return $value;
    }
}