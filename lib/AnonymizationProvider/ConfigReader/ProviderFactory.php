<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader;

use PayU\MysqlDumpAnonymizer\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\InterfaceProviderBuilder;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\YamlProviderBuilder;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\ValueAnonymizerFactory;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

final class ProviderFactory implements ProviderFactoryInterface
{

    public const DEFAULT_CONFIG_TYPE = self::YAML_CONFIG;

    public const YAML_CONFIG = 'yaml';

    /**
     * @var CommandLineParameters
     */
    private $commandLineParameters;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(CommandLineParameters $commandLineParameters, ConfigInterface $config)
    {
        $this->commandLineParameters = $commandLineParameters;
        $this->config = $config;
    }

    public function make(): InterfaceProviderBuilder
    {

        if ($this->commandLineParameters->getConfigType() === self::YAML_CONFIG) {
            return new YamlProviderBuilder(
                $this->commandLineParameters->getConfigFile(),
                new Parser(),
                new ValueAnonymizerFactory($this->config),
                $this->commandLineParameters->getOnNotConfiguredTable(),
                $this->commandLineParameters->getOnNotConfiguredColumn()
            );
        }

        throw new RuntimeException('Cannot build config');
    }
}
