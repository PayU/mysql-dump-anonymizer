<?php

namespace PayU\MysqlDumpAnonymizer\Provider;


//TODO implement it on AnonymizationProvider class and change signature
interface AnonymizationConfigInterface {

 /**
     * @param string $table
     * @param string $action
     * @param ColumnAnonymizationProvider[]
     */
    public function addTableConfig($table, $action, $columns);

    public function getTableConfig($table);

}
