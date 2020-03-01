<?php

namespace PayU\MysqlDumpAnonymizer;

use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use PayU\MysqlDumpAnonymizer\Provider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\Services\ProviderFactory;
use PayU\MysqlDumpAnonymizer\Services\LineParser\LineParserInterface;
use PayU\MysqlDumpAnonymizer\Services\LineParserFactory;

class Setup {

    /** @var CommandLineParameters */
    private $commandLineParameters;

    /** @var Observer */
    private $observer;

    /** @var ProviderFactory */
    private $providerFactory;

    /** @var LineParserFactory */
    private $lineParserFactory;

    public function __construct(CommandLineParameters $commandLineParameters, Observer $observer)
    {
        $this->commandLineParameters = $commandLineParameters;
        $this->observer = $observer;
        $this->providerFactory = new ProviderFactory($commandLineParameters);
        $this->lineParserFactory = new LineParserFactory();
    }

    /**\
     * @param resource $errorStream
     * @return array<AnonymizationProviderInterface,LineParserInterface>
     */
    public function setup($errorStream): array
    {
        try {
            $this->commandLineParameters->setCommandLineArguments($_SERVER['argv']);
            $this->commandLineParameters->validate();

            if ($this->commandLineParameters->isShowProgress()) {
                $this->observer->registerObserver(new Observer\Progress());
            }

            $providerBuilder = $this->providerFactory->make();
            $providerBuilder->validate();
            $anonymizationProvider = $providerBuilder->buildProvider();
            $lineParser = $this->lineParserFactory->chooseLineParser($this->commandLineParameters->getLineParser());

            return [$anonymizationProvider, $lineParser];

        } catch (InvalidArgumentException | ConfigValidationException $e) {
            fwrite($errorStream, 'ERROR: ' . $e->getMessage() . "\n");
        }

        fwrite($errorStream, CommandLineParameters::help());
        exit(1);
    }


}