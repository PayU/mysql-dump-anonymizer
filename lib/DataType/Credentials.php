<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Credentials implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}