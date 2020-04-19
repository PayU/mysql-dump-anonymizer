<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class BankData implements ValueAnonymizerInterface
{

    /**
     * @var StringHashInterface
     */
    private $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());  //123  ; '123' // NULL 'NULL'
        }

        $anonymizedEscapedValue = $this->stringHash->hashMe($value->getUnEscapedValue());

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
