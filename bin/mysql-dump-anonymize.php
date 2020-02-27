<?php
use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\Entity\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\Entity\DataTypes;
use PayU\MysqlDumpAnonymizer\Observer;
use PayU\MysqlDumpAnonymizer\Services\ConfigFactory;
use PayU\MysqlDumpAnonymizer\Services\DataTypeService;
use PayU\MysqlDumpAnonymizer\Services\LineParserFactory;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

//TODO put command line param and here config
$commandLineParameters = new CommandLineParameters();
$commandLineParameters->setCommandLineArguments($_SERVER['argv']);
$commandLineParameters->validate();

$observer = new Observer();
if ($commandLineParameters->isShowProgress()) {
    $observer->registerObserver(new Observer\Progress());
}

$application = new Anonymizer(
    $commandLineParameters,
    new ConfigFactory(),
    new LineParserFactory(),
    new DataTypeService(new DataTypes()),
    $observer
);


$application->run(STDIN, STDOUT, STDERR);

