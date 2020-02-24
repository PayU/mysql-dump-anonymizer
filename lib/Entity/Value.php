<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;


final class Value
{
    /** @var string raw value in insert statement */
    private $quotedValue;

    /** @var string parsed value that should be anonymized */
    private $value;

    public function __construct(string $rawValue, string $value)
    {
        $this->quotedValue = $rawValue;
        $this->value = $value;
    }

    public function getQuotedValue(): string
    {
        return $this->quotedValue;
    }

    public function getValue(): string
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
