<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application;

class Timer
{
    private string $name;
    private float $startTime;
    /** @var callable */
    private $stopCallback;

    public function __construct(string $name, callable $stopCallback)
    {
        $this->name = $name;
        $this->stopCallback = $stopCallback;
        $this->startTime = microtime(true);
    }

    public function stop(): void
    {
        ($this->stopCallback)($this->name, microtime(true) - $this->startTime);
    }

}
