<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class SensitiveFreeText implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}