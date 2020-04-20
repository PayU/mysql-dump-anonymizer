<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

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

        $anonymizedEscapedValue = $this->stringHash->hashMe($value->getUnEscapedValue());
        return AnonymizedValue::fromUnescapedValue($anonymizedEscapedValue);
    }
}
