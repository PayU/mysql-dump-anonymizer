<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;

final class FreeText implements ValueAnonymizerInterface
{

    private StringHashInterface $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }

    /**
     * @param Value $value
     * @param Value[] $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ($value->isExpression()) {
            return AnonymizedValue::fromOriginalValue($value);
        }

        $string = $value->getUnEscapedValue();
        if (strlen($string) < 12) {
            $anonymizedString = $this->stringHash->hashKeepFormat(
                substr($this->stringHash->sha256($string), 0, 12)
            );
        } else {
            $anonymizedString = $this->stringHash->hashKeepFormat($value->getUnEscapedValue());
        }

        return AnonymizedValue::fromUnescapedValue($anonymizedString);
    }
}
