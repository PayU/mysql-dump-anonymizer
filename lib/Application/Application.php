<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application;

use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;

class Application
{
    public static function run(): void
    {
        $timer = TimeStats::start('setup');
        $commandLineParameters = new CommandLineParameters();
        $observer = new Observer();
        $timer->stop();

        try {
            $timer = TimeStats::start('setup');
            $setup = new Setup($commandLineParameters, $observer);
            $setup->setup();

            $observer->notify(Observer::EVENT_BEGIN, $commandLineParameters->getEstimatedDumpSize());

            $application = new Anonymizer(
                $setup->getAnonymizationProvider(),
                $setup->getLineParser(),
                $setup->getLineDump(),
                $observer
            );

            $timer->stop();
            $timer = TimeStats::start('run');
            $application->run(STDIN, STDOUT);
            $timer->stop();
        } catch (InvalidArgumentException | ConfigValidationException $e) {
            fwrite(STDERR, 'ERROR: ' . $e->getMessage() . "\n");
            fwrite(STDERR, $commandLineParameters->help());
            $timer->stop();
            exit(1);
        }
    }
}
