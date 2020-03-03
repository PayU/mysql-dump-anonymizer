<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class FreeText implements ValueAnonymizerInterface
{
    public function anonymize(Value $value, array $row, ConfigInterface $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $string = $value->getUnEscapedValue();
        if (strlen($string) < 12) {
            $anonymizedString = $config->getHashStringHelper()->hashMe(
                substr($config->getHashStringHelper()->sha256($string), 0 ,12)
            );
        }else{
            $anonymizedString = $config->getHashStringHelper()->hashMe($value->getUnEscapedValue());
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedString));
    }
}
