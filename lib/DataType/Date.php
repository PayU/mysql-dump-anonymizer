<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Date implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}