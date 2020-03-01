<?php

namespace PayU\MysqlDumpAnonymizer\Provider;


use PayU\MysqlDumpAnonymizer\ValueAnonymizer\ValueAnonymizerInterface;

interface AnonymizationProviderInterface {

    public function getTableAction($table);

    public function getAnonymizationFor($table, $column) : ValueAnonymizerInterface;

}
