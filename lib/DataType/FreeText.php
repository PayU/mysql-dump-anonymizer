<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

class FreeText implements InterfaceDataType
{

    public function anonymize($value)
    {
        return 'FFFF'.$value;
    }

}