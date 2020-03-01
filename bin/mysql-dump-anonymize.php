<?php
use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Observer;
use PayU\MysqlDumpAnonymizer\Setup;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$commandLineParameters = new CommandLineParameters();
$observer = new Observer();
$config = new Config();


[$anonymizationProvider, $lineParser] = (new Setup($commandLineParameters, $observer))->setup(STDERR);

$application = new Anonymizer(
    $commandLineParameters,
    $anonymizationProvider,
    $lineParser,
    $observer,
    $config
);

$application->run(STDIN, STDOUT);

