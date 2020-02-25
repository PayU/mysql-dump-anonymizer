<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;


final class Value
{
    /** @var string raw value in insert statement */
    private $quotedValue;

    /**
     * Needs to be able to make the difference between the string NULL and the actual NULL to set in the query string
     * INSERT INTO .. ('NULL',NULL,'0xHEX',0xHEX)
     *
     * @TODO refactor the current way !
     *
     * @var DatabaseValue
     */
    private $value;

    public function __construct(string $rawValue, DatabaseValue $value)
    {
        $this->quotedValue = $rawValue;
        $this->value = $value;
    }

    public function getQuotedValue(): string
    {
        return $this->quotedValue;
    }

    public function getValue() : DatabaseValue
    {
        return $this->value;
    }

    /**
     * @param string $quotedValue
     */
    public function setQuotedValue(string $quotedValue): void
    {
        $this->quotedValue = $quotedValue;
    }



}
