<?php

namespace PayU\MysqlDumpAnonymizer\Services;

use PayU\MysqlDumpAnonymizer\Services\ConfigBuilder\InterfaceConfigBuilder;
use PayU\MysqlDumpAnonymizer\Services\ConfigBuilder\YamlConfig;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

class ConfigFactory
{

    public const DEFAULT_CONFIG_TYPE = self::YAML_CONFIG;

    public const YAML_CONFIG = 'yaml';

    public function make(string $configType, string $configFile): InterfaceConfigBuilder
    {

        if ($configType === self::YAML_CONFIG) {
            [$anonFile, $noAnonFile] = explode(',', $configFile, 2);
            return new YamlConfig($anonFile, $noAnonFile, new Parser());
        }

        throw new RuntimeException('Cannot build config');

    }

}