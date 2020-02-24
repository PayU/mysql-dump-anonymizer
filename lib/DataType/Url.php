<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Url implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}