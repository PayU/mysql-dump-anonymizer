<?php


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader;


use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\InterfaceProviderBuilder;

interface ProviderFactoryInterface
{
    public function make(): InterfaceProviderBuilder;
}