<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class Username implements ValueAnonymizerInterface
{
    public function anonymize(Value $value, array $row, ConfigInterface $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedValue = $value->getUnEscapedValue();

        //we want the anonymizedValue length to be at least 12
        if (strlen($unescapedValue) >= 12) {
            $anonymizedEscapedValue = $config->getHashStringHelper()->hashMe($unescapedValue);
        } else {
            $anonymizedEscapedValue = $config->getHashStringHelper()->hashMe(
                substr($config->getHashStringHelper()->sha256($unescapedValue), 0, 12)
            );
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
