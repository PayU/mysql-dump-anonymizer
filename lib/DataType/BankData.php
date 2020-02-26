<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;

class BankData implements InterfaceDataType
{

    public function anonymize(Value $value): Value
    {
        if ($value->isExpression()) {
            return $value;
        }

        $newValue = 'ANON'.$value->getUnEscapedValue();

        $value->setRawValue(EscapeString::escape($newValue));

        return $value;
    }
}