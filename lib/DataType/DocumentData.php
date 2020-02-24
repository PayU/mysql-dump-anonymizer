<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class DocumentData implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}