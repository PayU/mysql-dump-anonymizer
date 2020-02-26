<?php

namespace PayU\MysqlDumpAnonymizer\DataType;




use PayU\MysqlDumpAnonymizer\Entity\Value;

class Email implements InterfaceDataType
{

    public function anonymize(Value $value): Value
    {
        return $value;
    }
}