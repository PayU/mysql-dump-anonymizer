<?php

namespace PayU\MysqlDumpAnonymizer\Services;

use PayU\MysqlDumpAnonymizer\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\Provider\InterfaceProviderBuilder;
use PayU\MysqlDumpAnonymizer\Provider\YamlProviderBuilder;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

class ProviderFactory
{

    public const DEFAULT_CONFIG_TYPE = self::YAML_CONFIG;

    public const YAML_CONFIG = 'yaml';

    /**
     * @var CommandLineParameters
     */
    private $commandLineParameters;

    public function __construct(CommandLineParameters $commandLineParameters)
    {
        $this->commandLineParameters = $commandLineParameters;
    }

    public function make(): InterfaceProviderBuilder
    {

        if ($this->commandLineParameters->getConfigType() === self::YAML_CONFIG) {
            return new YamlProviderBuilder(
                $this->commandLineParameters->getConfigFile(),
                new Parser(),
                new DataTypeFactory(),
                $this->commandLineParameters->getOnNotConfiguredTable(),
                $this->commandLineParameters->getOnNotConfiguredColumn()
            );
        }

        throw new RuntimeException('Cannot build config');

    }

}