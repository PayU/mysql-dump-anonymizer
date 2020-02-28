<?php

namespace PayU\MysqlDumpAnonymizer\Provider;

final class AnonymizationTableConfig {

    private $action;

    /** @var ColumnAnonymizationProvider[]|null */
    private $columns;

    /**
     * AnonymizationAction constructor.
     * @param $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function addColumn($columnName, ColumnAnonymizationProvider $columnConfig) {
        $this->columns[$columnName] = $columnConfig;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @return ColumnAnonymizationProvider[]|null
     * TODO use columname directly
     */
    public function getColumns() : ?array {
        return $this->columns;
    }




}