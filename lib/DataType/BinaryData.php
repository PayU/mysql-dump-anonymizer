<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class BinaryData implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}