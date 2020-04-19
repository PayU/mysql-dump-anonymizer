<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


use PayU\MysqlDumpAnonymizer\Entity\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class FileName implements ValueAnonymizerInterface
{

    private StringHashInterface $stringHash;

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

        $nameWithoutExtension = substr($unescapedValue, 0, (int)strrpos($unescapedValue, '.'));
        $extension = substr($unescapedValue, strrpos($unescapedValue, '.') + 1);

        $anonymizedNameWithoutExtension = $this->stringHash->hashMe($nameWithoutExtension);

        return new AnonymizedValue(EscapeString::escape($anonymizedNameWithoutExtension . '.'.$extension));
    }
}
