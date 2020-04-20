<?php

declare(strict_types=1);

use PayU\MysqlDumpAnonymizer\Application\Application;

require_once dirname(__DIR__).'/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

Application::run();
