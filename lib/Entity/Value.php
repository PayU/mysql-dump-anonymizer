<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;


final class Value
{
    /** @var string raw value in insert statement */
    private $rawValue;

     /* @var string */
    private $unEscapedValue;

    /**
     * Needs to be able to make the difference between the string NULL and the actual NULL to set in the query string
     * INSERT INTO .. ('NULL',NULL,'0xHEX',0xHEX)
     *
     * @var bool
     *
     */
    private $isExpression;

    public function __construct(string $rawValue, string $unEscapedValue, bool $isExpression)
    {
        $this->rawValue = $rawValue; // This will be ['a\'b\"\\n\\r\\t'] or [NULL] or [0xFEFE]
        $this->unEscapedValue = $unEscapedValue; // This will be  [a'b"\n\r\t] or [NULL] or [0xFEFE]
        $this->isExpression = $isExpression; //For the above, this will be false,true,true
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getUnEscapedValue(): string
    {
        return $this->unEscapedValue;
    }

    /**
     * @return bool
     */
    public function isExpression(): bool
    {
        return $this->isExpression;
    }

    /**
     * @param string $rawValue
     */
    public function setRawValue(string $rawValue): void
    {
        $this->rawValue = $rawValue;
    }





}
