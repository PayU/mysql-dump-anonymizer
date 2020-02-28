<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;
use PayU\MysqlDumpAnonymizer\Services\StringHash;

class FileName implements InterfaceDataType
{
    public function anonymize(Value $value): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedValue = $value->getUnEscapedValue();

        $nameWithoutExtension = substr($unescapedValue, 0, strpos($unescapedValue, '.'));
        $extension = substr($unescapedValue, strpos($unescapedValue, '.') + 1);

        $anonymizedNameWithoutExtension = (new StringHash('the@salt--'))->hashMe($nameWithoutExtension);

        return new AnonymizedValue(EscapeString::escape($anonymizedNameWithoutExtension . $extension));
    }

}