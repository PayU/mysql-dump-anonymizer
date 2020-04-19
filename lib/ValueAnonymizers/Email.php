<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Entity\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Email implements ValueAnonymizerInterface
{
    private StringHashInterface $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ($value->isExpression() && $value->getRawValue() === 'NULL') {
            return new AnonymizedValue($value->getRawValue());
        }

        if ($value->getRawValue() === 'NULL') {
            return new AnonymizedValue($value->getRawValue());
        }

        $anonymizedEscapedValue = $this->stringHash->hashMe($value->getUnEscapedValue());

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
