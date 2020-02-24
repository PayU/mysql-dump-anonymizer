<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Ip implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}