<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;
use PayU\MysqlDumpAnonymizer\Services\StringHash;

class CardData implements InterfaceDataType
{
    public function anonymize(Value $value): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $anonymizedEscapedValue = (new StringHash('the@salt--'))->hashMe($value->getUnEscapedValue());

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }

}