<?php

namespace PayU\MysqlDumpAnonymizer\DataType;




use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;
use PayU\MysqlDumpAnonymizer\Services\StringHash;

class FreeText implements InterfaceDataType
{

    public function anonymize(Value $value): Value
    {
        if ($value->isExpression()) {
            return $value;
        }

        $escapedValue = (new StringHash('the@salt--'))->hashMe($value->getUnEscapedValue());

        $value->setRawValue(EscapeString::escape($escapedValue));

        return $value;
    }

}