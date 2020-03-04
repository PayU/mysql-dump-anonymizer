<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use PayU\MysqlDumpAnonymizer\LineDump\LineDump;
use PayU\MysqlDumpAnonymizer\LineDump\MysqlLineDump;
use PayU\MysqlDumpAnonymizer\Provider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\Services\ProviderFactory;
use PayU\MysqlDumpAnonymizer\Services\LineParser\LineParserInterface;
use PayU\MysqlDumpAnonymizer\Services\LineParserFactory;

class Setup
{
    /** @var CommandLineParameters */
    private $commandLineParameters;

    /** @var Observer */
    private $observer;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(CommandLineParameters $commandLineParameters, Observer $observer, ConfigInterface $config)
    {
        $this->commandLineParameters = $commandLineParameters;
        $this->observer = $observer;
        $this->config = $config;
    }

    public function setup(): void
    {
        $this->commandLineParameters->setCommandLineArguments($_SERVER['argv']);
        $this->commandLineParameters->validate();

        if ($this->commandLineParameters->isShowProgress()) {
            $this->observer->registerObserver(new Observer\Progress());
        }
    }

    public function getLineParser(): LineParserInterface
    {
        return (new LineParserFactory())->chooseLineParser($this->commandLineParameters->getLineParser());
    }

    /**
     * @return AnonymizationProviderInterface
     * @throws ConfigValidationException
     */
    public function getAnonymizationProvider(): AnonymizationProviderInterface
    {
        $providerBuilder = (new ProviderFactory($this->commandLineParameters, $this->config))->make();
        $providerBuilder->validate();
        return $providerBuilder->buildProvider();
    }

    public function getLineDump(): LineDump
    {
        return new MysqlLineDump();
    }

}
