<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application;

use PayU\MysqlDumpAnonymizer\Application\Observer\ProcessObserverInterface;

interface ObserverInterface
{
    public function registerObserver(ProcessObserverInterface $observer): void;

    public function notify($event, $data) : void;
}
