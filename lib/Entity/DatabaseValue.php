<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Entity;

class DatabaseValue {

    /** @var string */
    private $value;

    /**
     * @var bool
     */
    private $isExpression;

    public function __construct(string $value, bool $isExpression)
    {
        $this->value = $value;
        $this->isExpression = $isExpression;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isExpression(): bool
    {
        return $this->isExpression;
    }

    /**
     * @param string $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @param bool $isExpression
     */
    public function setIsExpression(bool $isExpression): void
    {
        $this->isExpression = $isExpression;
    }





}