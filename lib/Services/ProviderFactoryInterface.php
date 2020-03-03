<?php


namespace PayU\MysqlDumpAnonymizer\Services;


use PayU\MysqlDumpAnonymizer\Provider\InterfaceProviderBuilder;

interface ProviderFactoryInterface
{
    public function make(): InterfaceProviderBuilder;
}