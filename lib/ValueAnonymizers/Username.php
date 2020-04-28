<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;

final class Username implements ValueAnonymizerInterface
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

        $unescapedValue = $value->getUnEscapedValue();

        //we want the anonymizedValue length to be at least 12
        if (strlen($unescapedValue) >= 12) {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat($unescapedValue);
        } else {
            $anonymizedEscapedValue = $this->stringHash->hashKeepFormat(
                substr($this->stringHash->sha256($unescapedValue), 0, 12)
            );
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedEscapedValue);
    }
}
