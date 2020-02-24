<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class BankData implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}