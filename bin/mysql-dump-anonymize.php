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

$out = "Done.\nAnonymizer peak memory usage: " . number_format(memory_get_peak_usage()/1024/1024, 3, '.', '') . " MB\n";
fwrite(STDERR, $out);
