<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\Entity\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class FreeText implements ValueAnonymizerInterface
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
        if (strlen($string) < 12) {
            $anonymizedString = $this->stringHash->hashMe(
                substr($this->stringHash->sha256($string), 0 ,12)
            );
        }else{
            $anonymizedString = $this->stringHash->hashMe($value->getUnEscapedValue());
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedString);
    }
}
