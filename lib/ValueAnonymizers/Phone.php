<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;

final class Phone implements ValueAnonymizerInterface
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
            $hash = base_convert($this->stringHash->sha256($string), 16, 10);
            $toAnonymize = '+'.substr($hash, 0, 11);
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($toAnonymize);
        } else {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($string);
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedEscapedValue);
    }
}
