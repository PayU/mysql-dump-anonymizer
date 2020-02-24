<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class FileName implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}