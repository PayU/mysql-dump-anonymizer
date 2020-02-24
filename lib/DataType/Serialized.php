<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Serialized implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}