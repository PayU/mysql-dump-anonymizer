<?php

declare(strict_types=1);

use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use PayU\MysqlDumpAnonymizer\Observer;
use PayU\MysqlDumpAnonymizer\Setup;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

$commandLineParameters = new CommandLineParameters();
$observer = new Observer();
$config = new Config();

try {

    $setup = new Setup($commandLineParameters, $observer, $config);
    $setup->setup();

    $observer->notify(Observer::EVENT_BEGIN, $commandLineParameters->getEstimatedDumpSize());

    $application = new Anonymizer(
        $setup->getAnonymizationProvider(),
        $setup->getLineParser(),
        $setup->getLineDump(),
        $observer,
        $config
    );

    $application->run(STDIN, STDOUT);

} catch (InvalidArgumentException | ConfigValidationException $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . "\n");
    fwrite(STDERR, CommandLineParameters::help());
    exit(1);
}

