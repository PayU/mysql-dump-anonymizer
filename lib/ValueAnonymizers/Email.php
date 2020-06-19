<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;

final class Email implements ValueAnonymizerInterface
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

        $string = $value->getUnEscapedValue();

        if (strlen($string) < 10) {
            $hash = $this->stringHash->sha256($string);
            $toAnonymize = substr($hash, 0, 10);
            $toAnonymize .= '@'.substr($hash, 10, 5).'.'.substr($hash, 15, 3);
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($toAnonymize);
        } else {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($string);
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedEscapedValue);
    }
}
