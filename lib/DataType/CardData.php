<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class CardData implements InterfaceDataType
{

    public function anonymize($value)
    {
        return $value;
    }
}