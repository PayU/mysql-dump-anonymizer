<?php

namespace PayU\MysqlDumpAnonymizer\Provider;


final class AnonymizationProvider {

    /** @var AnonymizationTableConfig[]*/
    private $tables = [];

    /**
     * @param string $table
     * @param string $action
     */
    public function addConfig($table, $action) : void {
        if (!array_key_exists($table, $this->tables)) {
            $this->tables[$table] = new AnonymizationTableConfig($action);
        }
    }

    public function getActionConfig($table) : AnonymizationTableConfig {
        return $this->tables[$table];
    }


}