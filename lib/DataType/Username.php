<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Username implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}