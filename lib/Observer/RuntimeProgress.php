<?php

namespace PayU\MysqlDumpAnonymizer;


class RuntimeProgress
{
    private const FULL = "\u{2588}";
    private const EMPTY = "\u{2591}";

    public static $output = STDERR;


    public static $start = 0;
    public static $readTime = 0;
    public static $writeTime = 0;
    public static $parseTime = 0;
    public static $anonymizationTime = 0;
    public static $anonymizationTimeDataTypes = [];
    public static $anonymizationTimeDataTypesCount = [];

    private static $memoryLimit = null;

    public static function output($string)
    {
        fwrite(self::$output, $string);
    }

    private static function round($microseconds, $decimals = 2, $pad = 6) {
        return str_pad(
            number_format(round($microseconds,$decimals),$decimals),$pad,' ',STR_PAD_LEFT);
    }

    public static function show($read, $total)
    {

        if (self::$memoryLimit === null) {
            $memory_limit = ini_get('memory_limit');
            self::$memoryLimit = $memory_limit;
            if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
                if ($matches[2] === 'M') {
                    self::$memoryLimit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
                } else if ($matches[2] === 'K') {
                    self::$memoryLimit = $matches[1] * 1024; // nnnK -> nnn KB
                } else if ($matches[2] === 'G') {
                    self::$memoryLimit = $matches[1] * 1024 * 1024 * 1024; // nnnK -> nnn KB
                }
            }

        }

        $tot = microtime(true) - self::$start;

        $percent = $read / $total * 100;
        $perc = round($percent, 0);
        self::showProgressBar(self::FULL, self::EMPTY, $perc);
        $str = $read . '/' . $total . ' bytes (' . (round($percent, 4)) . '%)  ';
        $eta = floor($tot * 100 / $percent);

        self::output('  ' . $str);
        self::output(PHP_EOL);

        $usedMem = memory_get_peak_usage(1);

        self::output(self::pad('Used Memory'). self::round($usedMem,0, 15).' / '.self::round(self::$memoryLimit,0, 15).' ('.self::round($usedMem / self::$memoryLimit*100).'%) '.PHP_EOL);

        self::output(self::pad('Running time').self::round($tot).'s '.PHP_EOL
        .self::pad('Read time').self::round(self::$readTime).'s ('.self::round(self::$readTime/$tot*100).'%) ETA:'.$eta.'s '.PHP_EOL
        .self::pad('Write time').self::round(self::$writeTime).'s ('.self::round(self::$writeTime/$tot*100).'%) '.PHP_EOL
        .self::pad('Parse time').self::round(self::$parseTime).'s ('.self::round(self::$parseTime/$tot*100).'%)   '.PHP_EOL
        .self::pad('TOTAL Anon').self::round(self::$anonymizationTime).'s ('.self::round(self::$anonymizationTime/$tot*100).'%)      '.PHP_EOL.PHP_EOL);
        $goup = 8;

        $output = '';
        foreach (self::$anonymizationTimeDataTypes as $dataType=>$microtime) {

            if (self::$anonymizationTime > 0) {
                $div = $microtime / self::$anonymizationTime;
            }else{
                $div = 0;
            }
            $output .= self::pad($dataType).self::round($microtime).'s ('.self::round($div*100).'%)  '
            .' (x '.self::round(self::$anonymizationTimeDataTypesCount[$dataType], 0).')'.PHP_EOL;
            $goup++;
        }
        self::output($output);

        //go back where you printed first
        self::output("\r");
        self::goUp($goup);
    }

    private static function pad($string) {
        return str_pad($string, 30, ' ', STR_PAD_LEFT).':';
    }

    private static function showProgressBar($full, $empty, $percentage)
    {
        $progress = '';
        for ($i = 1; $i <= 100; $i++) {
            if ($i <= $percentage) {
                $progress .= $full;
            } else {
                $progress .= $empty;
            }
        }
        self::output('  ' . $progress . ' ' . $percentage);
    }

    public static function goUp($times = 1)
    {
        self::output(chr(27) . '[' . $times . 'A');
    }

}