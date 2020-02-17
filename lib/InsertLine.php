<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer;

class InsertLine
{
    /** @var string */
    private $table;

    /** @var string[] */
    private $columns;

    /** @var Value[][] */
    private $valuesList;

    /**
     * InsertLine constructor.
     * @param string $table
     * @param string[] $columns
     * @param Value[][] $valuesList
     */
    public function __construct(string $table, array $columns, array $valuesList)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->valuesList = $valuesList;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Value[][]
     */
    public function getValuesList(): array
    {
        return $this->valuesList;
    }

}
