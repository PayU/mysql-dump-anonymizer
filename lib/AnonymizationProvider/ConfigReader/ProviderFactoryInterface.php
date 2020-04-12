<?php


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader;


interface ProviderFactoryInterface
{
    public function make(): InterfaceProviderBuilder;
}