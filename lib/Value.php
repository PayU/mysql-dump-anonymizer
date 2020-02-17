<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;


final class Value
{
    /** @var string raw value in insert statement */
    private $rawValue;
    /** @var string parsed value that should be anonymized */
    private $value;

    public function __construct(string $rawValue, string $value)
    {
        $this->rawValue = $rawValue;
        $this->value = $value;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

}
