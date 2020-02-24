<?php

namespace PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig;


final class AnonymizationActionConfig {

    private $action;

    /** @var AnonymizationColumnConfig[]|null */
    private $columns;

    /**
     * AnonymizationAction constructor.
     * @param $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function addColumn($columnName, AnonymizationColumnConfig $columnConfig) {
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
     * @return AnonymizationColumnConfig[]|null
     */
    public function getColumns() : ?array {
        return $this->columns;
    }




}