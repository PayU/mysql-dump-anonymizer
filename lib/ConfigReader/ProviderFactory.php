<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ConfigReader;

use RuntimeException;
use Symfony\Component\Yaml\Parser;

final class ProviderFactory implements ProviderFactoryInterface
{

    public const DEFAULT_CONFIG_TYPE = self::YAML_CONFIG;

    public const YAML_CONFIG = 'yaml';

    public function make($configType, $configFile): InterfaceProviderBuilder
    {

        if ($configType === self::YAML_CONFIG) {
            return new YamlProviderBuilder(
                $configFile,
                new Parser(),
                new ValueAnonymizerFactory()
            );
        }

        throw new RuntimeException('Cannot build config');
    }
}
