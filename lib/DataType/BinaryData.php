<?php

namespace PayU\MysqlDumpAnonymizer\DataType;


use PayU\MysqlDumpAnonymizer\Entity\DatabaseValue;

class BinaryData implements InterfaceDataType
{

    public function anonymize(DatabaseValue $value): DatabaseValue
    {
        return $value;
    }
}