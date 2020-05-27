<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application;

require_once dirname(__DIR__).'/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');

Application::run();
