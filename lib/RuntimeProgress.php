<?php

namespace PayU\MysqlDumpAnonymizer;


class RuntimeProgress {

    const FULL = "█";
    const EMPTY = "░";


    private static function output($string, $outputStream) {
        fwrite($outputStream, $string);
    }

    public static function show($read, $total, $outputStream = STDERR) {
        self::output("\r", $outputStream);
        self::output(chr(8), $outputStream);

        self::output("\r", $outputStream);
        $perc = round($read / $total * 100, 0);
        self::showProgressBar(self::FULL, self::EMPTY, $perc, $outputStream);
        //self::output(PHP_EOL, $outputStream);
        $str = $read . '/' . $total . ' bytes (' . (round($read / $total * 100, 4)) . '%)                              ';
        self::output('  ' . $str, $outputStream);
    }


    private static function showProgressBar($full, $empty, $percentage, $outputStream)
    {
        $progress = '';
        for ($i = 1; $i <= 100; $i++) {
            if ($i <= $percentage) {
                $progress .= $full;
            } else {
                $progress .= $empty;
            }
        }
        self::output( '  ' . $progress . ' ' . $percentage, $outputStream);
    }
}