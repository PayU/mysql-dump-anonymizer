<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Phone implements ValueAnonymizerInterface
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

        //  QWE123
        //  AFE381

        // '123123123' --  123123123
        //


        $anonymizedEscapedValue = $this->stringHash->hashMe($value->getUnEscapedValue());

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
