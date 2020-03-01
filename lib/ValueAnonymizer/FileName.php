<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class FileName implements ValueAnonymizerInterface
{
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedValue = $value->getUnEscapedValue();

        $nameWithoutExtension = substr($unescapedValue, 0, strpos($unescapedValue, '.'));
        $extension = substr($unescapedValue, strpos($unescapedValue, '.') + 1);

        $anonymizedNameWithoutExtension = $config->getHashStringHelper()->hashMe($nameWithoutExtension);

        return new AnonymizedValue(EscapeString::escape($anonymizedNameWithoutExtension . $extension));
    }

}