<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;

final class SensitiveFreeText implements ValueAnonymizerInterface
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

        if (strlen($string) <= 10) {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat(
                substr($this->stringHash->sha256($string), 0, 10)
            );
        } else {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($string);
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedEscapedValue);
    }
}
