<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application;

class TimeStats
{
    /** @var array<string, float> */
    private static array $time = [];

    private static $stopCallback;

    public static function staticInitialize()
    {
        self::$stopCallback = function (string $name, float $diffTime) {
            self::$time[$name] += $diffTime;
        };
    }

    public static function start($name): Timer
    {
        if (!isset(self::$time[$name])) {
            self::$time[$name] = 0.;
        }
        return new Timer($name, self::$stopCallback);
    }

    /**
     * @return array<string, float>
     */
    public static function getStats(): array
    {
        ksort(self::$time);
        return self::$time;
    }
}
