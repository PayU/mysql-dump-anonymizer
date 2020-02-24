<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class Email implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}