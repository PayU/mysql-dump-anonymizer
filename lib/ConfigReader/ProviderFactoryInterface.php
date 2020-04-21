<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ConfigReader;


interface ProviderFactoryInterface
{
    public function make($configType, $configFile): InterfaceProviderBuilder;
}