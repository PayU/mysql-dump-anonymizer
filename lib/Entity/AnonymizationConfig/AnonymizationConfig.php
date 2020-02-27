<?php

namespace PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig;

//TODO make an interface for this
final class AnonymizationConfig {

    /** @var AnonymizationActionConfig[]*/
    private $tables = [];

    /**
     * @param string $table
     * @param string $action
     */
    public function addConfig($table, $action) : void {
        if (!array_key_exists($table, $this->tables)) {
            $this->tables[$table] = new AnonymizationActionConfig($action);
        }
    }

    public function getActionConfig($table) : AnonymizationActionConfig {
        return $this->tables[$table];
    }


}