<?php

namespace PayU\MysqlDumpAnonymizer\Entity;

final class LineInfo {

    private $isInsert;
    private $table;
    private $columns;

    public function __construct(bool $isInsert, ?string $table, ?array $columns)
    {
        $this->isInsert = $isInsert;
        $this->table = $table;
        $this->columns = $columns;
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
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }


}