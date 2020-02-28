<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;
use PayU\MysqlDumpAnonymizer\Services\StringHash;

class Url implements InterfaceDataType
{
    public function anonymize(Value $value): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedURL = $value->getUnEscapedValue();

        $scheme = parse_url($unescapedURL, PHP_URL_SCHEME);
        $host = parse_url($unescapedURL, PHP_URL_HOST);

        $anonymizedHost = (new StringHash('the@salt--'))->hashMe($host);

        return new AnonymizedValue(EscapeString::escape($scheme . '://' . $anonymizedHost));
    }

}