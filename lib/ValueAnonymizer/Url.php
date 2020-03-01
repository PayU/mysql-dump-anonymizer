<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class Url implements ValueAnonymizerInterface
{
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedURL = $value->getUnEscapedValue();

        $scheme = parse_url($unescapedURL, PHP_URL_SCHEME);
        $rest = substr($unescapedURL, strlen($scheme)+3);

        $anonymizedHost = $config->getHashStringHelper()->hashMe($rest);

        return new AnonymizedValue(EscapeString::escape($scheme . '://' . $anonymizedHost));
    }
}
