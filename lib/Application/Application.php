<?php

namespace PayU\MysqlDumpAnonymizer\Application;


use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;

class Application
{

    public static function run()
    {
        $commandLineParameters = new CommandLineParameters();
        $observer = new Observer();

        try {

            $setup = new Setup($commandLineParameters, $observer);
            $setup->setup();

            $observer->notify(Observer::EVENT_BEGIN, $commandLineParameters->getEstimatedDumpSize());

            $application = new Anonymizer(
                $setup->getAnonymizationProvider(),
                $setup->getLineParser(),
                $setup->getLineDump(),
                $observer
            );

            $application->run(STDIN, STDOUT);

        } catch (InvalidArgumentException | ConfigValidationException $e) {
            fwrite(STDERR, 'ERROR: ' . $e->getMessage() . "\n");
            fwrite(STDERR, CommandLineParameters::help());
            exit(1);
        }

    }

}