<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ReadDump;

final class LineInfo
{

    private $isInsert;
    private $table;
    private $columns;

    /**
     * @var iterable
     */
    private $valuesParser;

    public function __construct(bool $isInsert, ?string $table, ?array $columns, iterable $valuesParser)
    {
        $this->isInsert = $isInsert;
        $this->table = $table;
        $this->columns = $columns;
        $this->valuesParser = $valuesParser;
    }

    /**
     * @return bool
     */
    public function isInsert(): bool
    {
        return $this->isInsert;
    }


    /**
     * @return string
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getColumns(): ?array
    {
        return $this->columns;
    }

    /**
     * @return iterable
     */
    public function getValuesParser(): iterable
    {
        return $this->valuesParser;
    }


}
