<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application;

require_once dirname(__DIR__).'/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

TimeStats::staticInitialize();

Application::run();

$out = "Done.\nAnonymizer peak memory usage: " . number_format(memory_get_peak_usage()/1024/1024, 3, '.', '') . " MB\n";
fwrite(STDERR, $out);

$stats = TimeStats::getStats();

$out = print_r($stats, true);
fwrite(STDERR, $out);
