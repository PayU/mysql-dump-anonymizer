<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Id implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}