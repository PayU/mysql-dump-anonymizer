<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class Id implements ValueAnonymizerInterface
{
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $anonymizedEscapedValue = $config->getHashStringHelper()->hashMe($value->getUnEscapedValue());

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }

}