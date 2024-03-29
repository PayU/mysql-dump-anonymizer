<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Application;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\Application\Observer\Progress;
use PayU\MysqlDumpAnonymizer\ConfigReader\ProviderFactory;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserFactory;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashSha256;
use PayU\MysqlDumpAnonymizer\WriteDump\LineDumpInterface;
use PayU\MysqlDumpAnonymizer\WriteDump\MysqlLineDump;

class Setup implements SetupInterface
{
    private CommandLineParametersInterface $commandLineParameters;

    private ObserverInterface $observer;

    public function __construct(CommandLineParametersInterface $commandLineParameters, ObserverInterface $observer)
    {
        $this->commandLineParameters = $commandLineParameters;
        $this->observer = $observer;
    }

    public function setup(): void
    {
        $this->commandLineParameters->setCommandLineArguments();
        $this->commandLineParameters->validate();

        if ($this->commandLineParameters->isShowProgress()) {
            $this->observer->registerObserver(new Progress());
        }

        StringHashSha256::setSalt($this->commandLineParameters->getSalt());
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
        $providerBuilder = (new ProviderFactory())->make(
            $this->commandLineParameters->getConfigType(),
            $this->commandLineParameters->getConfigFile()
        );
        $providerBuilder->validate();
        return $providerBuilder->buildProvider();
    }

    public function getLineDump(): LineDumpInterface
    {
        return new MysqlLineDump();
    }
}
