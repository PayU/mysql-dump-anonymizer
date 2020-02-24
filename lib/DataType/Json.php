<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Json implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}