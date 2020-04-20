<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class Url implements ValueAnonymizerInterface
{

    private StringHashInterface $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }


    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ($value->isExpression()) {
            return AnonymizedValue::fromOriginalValue($value);
        }

        $unescapedURL = $value->getUnEscapedValue();

        $scheme = parse_url($unescapedURL, PHP_URL_SCHEME);

        if ($scheme !== null) {
            $host = substr($unescapedURL, strlen((string)$scheme) + 3);
            $anonymizedHost = $this->stringHash->hashMe($host);
            $anonymizedUrl = $scheme . '://' . $anonymizedHost;
        } else {
            $anonymizedUrl = $this->stringHash->hashMe($unescapedURL);
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedUrl);
    }
}
