<?php

use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\Dumper\InsertLineMysqlLikeDumper;
use PayU\MysqlDumpAnonymizer\Parser\InsertLineStringParser;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\ValueAnonymizerRegistry;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$anonymizer = new Anonymizer(
    new InsertLineStringParser(),
    new InsertLineMysqlLikeDumper(),
    new ValueAnonymizerRegistry()
);

$anonymizer->anonymize(STDIN, STDOUT);
