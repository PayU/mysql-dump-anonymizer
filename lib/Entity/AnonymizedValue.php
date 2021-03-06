<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;

final class AnonymizedValue
{
    private const QUOTE = '\'';
    private const ESCAPING_MAP = [
        '\\' => '\\\\',
        '\'' => '\\\'',
        "\0" => '\\0',
        "\r" => '\\r',
        "\n" => '\\n',
        "\t" => '\\t',
        "\x08" => '\\b',
        "\x1A" => '\\Z',
    ];

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
        $anonymizedValue->rawValue = self::QUOTE . strtr($value, self::ESCAPING_MAP) . self::QUOTE;
        return $anonymizedValue;
    }
}
