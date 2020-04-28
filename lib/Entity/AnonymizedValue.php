<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;

final class AnonymizedValue
{
    /** @var string raw value in insert statement */
    private string $rawValue;

    private function __construct()
    {
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public static function fromOriginalValue(Value $value): AnonymizedValue
    {
        return self::fromRawValue($value->getRawValue());
    }

    public static function fromRawValue(string $value): AnonymizedValue
    {
        $anonymizedValue = new self();
        $anonymizedValue->rawValue = $value;
        return $anonymizedValue;
    }
    public static function fromUnescapedValue(string $value): AnonymizedValue
    {
        $anonymizedValue = new self();
        $anonymizedValue->rawValue = '\'' . addcslashes($value, "'\\\n") . '\'';
        return $anonymizedValue;
    }
}
