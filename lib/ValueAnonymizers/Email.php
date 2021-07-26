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

        [$user, $domain] = explode('@', $string, 2);

        if (strlen($user) < 10) {
            $toAnonymize = substr($this->stringHash->sha256($string), 0, 10);
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($toAnonymize);
        } else {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($user);
        }

        $anonymizedEscapedValue .= '@' . $this->stringHash->hashKeepFormat($domain);

        return AnonymizedValue::fromUnescapedValue($anonymizedEscapedValue);
    }
}
