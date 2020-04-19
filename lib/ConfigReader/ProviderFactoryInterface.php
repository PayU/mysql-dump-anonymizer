<?php


namespace PayU\MysqlDumpAnonymizer\ConfigReader;


interface ProviderFactoryInterface
{
    public function make($configType, $configFile): InterfaceProviderBuilder;
}