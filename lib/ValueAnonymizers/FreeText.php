<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class FreeText implements ValueAnonymizerInterface
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
            return new AnonymizedValue($value->getRawValue());
        }

        $string = $value->getUnEscapedValue();
        if (strlen($string) < 12) {
            $anonymizedString = $this->stringHash->hashMe(
                substr($this->stringHash->sha256($string), 0 ,12)
            );
        }else{
            $anonymizedString = $this->stringHash->hashMe($value->getUnEscapedValue());
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedString));
    }
}
