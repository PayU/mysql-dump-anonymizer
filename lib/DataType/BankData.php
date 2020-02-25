<?php

namespace PayU\MysqlDumpAnonymizer\DataType;


use PayU\MysqlDumpAnonymizer\Entity\DatabaseValue;

class BankData implements InterfaceDataType
{

    public function anonymize(DatabaseValue $value): DatabaseValue
    {
        if ($value->isExpression()) {
            return $value;
        }

        $value->setValue('ANON'.$value->getValue());
        return $value;
    }
}