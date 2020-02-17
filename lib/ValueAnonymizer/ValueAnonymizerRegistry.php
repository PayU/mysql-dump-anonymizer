<?php


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizer;

/**
 * @todo Parse configuration files and return the correct anonymizer for the specific table and column. For flexibility, allow ValueAnonymizerProviders to be configured from outside library.
 *
 * @package PayU\MysqlDumpAnonymizer
 */
class ValueAnonymizerRegistry
{

    public function getAnonymizer($table, $column): ValueAnonymizer
    {
        return new SameValueAnonymizer();
    }
}
