<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class IpInt implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}