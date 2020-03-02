<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class Username implements ValueAnonymizerInterface
{
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedValue = $value->getUnEscapedValue();

        //we want the anonymizedValue length to be at least 7
        if (strlen($unescapedValue) >= 7) {
            $anonymizedEscapedValue = $config->getHashStringHelper()->hashMe($unescapedValue);
        } else {
            $anonymizedEscapedValue = substr($config->getHashStringHelper()->sha256($unescapedValue), 0, 7);
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
