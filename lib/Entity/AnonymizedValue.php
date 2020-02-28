<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;

final class AnonymizedValue
{
    /** @var string raw value in insert statement */
    private $rawValue;

    /**
     * AnonymizedValue constructor.
     * @param string $rawValue
     */
    public function __construct(string $rawValue)
    {
        $this->rawValue = $rawValue;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

}
