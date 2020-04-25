<?php


namespace PayU\MysqlDumpAnonymizer\ConfigReader;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;

interface ValueAnonymizerFactoryInterface
{
    public function getValueAnonymizerClass(string $string, array $constructArguments) : ValueAnonymizerInterface;
    public function valueAnonymizerExists(string $string) : bool;
}