<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Username implements ValueAnonymizerInterface
{

    /** @var StringHashInterface */
    private $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }


    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedValue = $value->getUnEscapedValue();

        //we want the anonymizedValue length to be at least 12
        if (strlen($unescapedValue) >= 12) {
            $anonymizedEscapedValue = $this->stringHash->hashMe($unescapedValue);
        } else {
            $anonymizedEscapedValue = $this->stringHash->hashMe(
                substr($this->stringHash->sha256($unescapedValue), 0, 12)
            );
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
