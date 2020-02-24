<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Phone implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}