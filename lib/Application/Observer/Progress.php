<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application\Observer;

final class Progress implements ProcessObserverInterface
{

    private const FULL = "\u{2588}";
    private const EMPTY = "\u{2591}";

    private static $output = STDERR;

    private $total = 0;
    private $startedAt;
    private $startReadLine;

    private $readTimeTotal = 0;

    private $anonymizationTypes = [];

    private $anonymizationTypeStart = 0;

    private $truncate = 0;

    private $notAnInsert = 0;

    private $insertLineNoNeedForAnonymization = 0;

    private $noAnonymization = 0;

    private $memoryLimit;

    /**
     * @var int
     */
    private $totalReadBytes = 0;
    /**
     * @var float|int|string
     */
    private $anonymizationTotal = 0;

    private $goup;


    public function onBegin($estimatedTotalInputLength): void
    {
        $this->memoryLimit = ini_get('memory_limit');

        switch (substr($this->memoryLimit, -1)) {
            case 'M':
                $this->memoryLimit = substr($this->memoryLimit, 0, -1) * 1024 * 1024;
                break;
            case 'G':
                $this->memoryLimit = substr($this->memoryLimit, 0, -1) * 1024 * 1024 * 1024;
                break;
            case 'K':
                $this->memoryLimit = substr($this->memoryLimit, 0, -1) * 1024;
                break;
        }

        $this->total = $estimatedTotalInputLength;
        $this->startedAt = microtime(true);
    }

    public function onStartReadLine(): void
    {
        $this->startReadLine = microtime(true);
    }

    public function onFinishReadLine(int $lineLength): void
    {
        $this->totalReadBytes += $lineLength;
        $this->readTimeTotal += (microtime(true) - $this->startReadLine);
    }

    public function onNullValue(string $anonymizationType): void
    {
        $this->getAnonymizationType($anonymizationType)['nulls']++;
    }

    public function onAnonymizationStart(string $anonymizationType, int $dataSize): void
    {
        $this->getAnonymizationType($anonymizationType)['count']++;
        $this->getAnonymizationType($anonymizationType)['size'] += $dataSize;

        $this->anonymizationTypeStart = microtime(true);
    }

    public function onAnonymizationFinish(string $anonymizationType): void
    {
        $anonymiationTime = (microtime(true) - $this->anonymizationTypeStart);
        $this->anonymizationTotal += $anonymiationTime;
        $this->getAnonymizationType($anonymizationType)['time'] += $anonymiationTime;
    }

    public function onNotAnInsertLine(): void
    {
        $this->notAnInsert++;
    }

    public function onTruncateLine(): void
    {
        $this->truncate++;
    }

    public function onNoAnonymization(): void
    {
        $this->noAnonymization++;
    }

    public function onNoNeedForAnonymizationInsertLine(): void
    {
        $this->insertLineNoNeedForAnonymization++;
    }

    public function onAfterEachLineProcessing(): void
    {
        $this->show();
    }

    public function onEnd(): void
    {
        $this->output(PHP_EOL);
    }


    private function &getAnonymizationType($key): array
    {
        $key = substr(strrchr($key, '\\'), 1);

        if (!array_key_exists($key, $this->anonymizationTypes)) {
            $this->anonymizationTypes[$key] = [
                'nulls' => 0,
                'time' => 0,
                'count' => 0,
                'size' => 0,
            ];
        }

        return $this->anonymizationTypes[$key];
    }

    private function output($string): void
    {
        fwrite(self::$output, $string);
    }

    private function round($microseconds, $decimals = 2, $pad = 6)
    {
        return str_pad(
            number_format((float)round($microseconds, $decimals), $decimals),
            $pad,
            ' ',
            STR_PAD_LEFT
        );
    }

    private function show(): void
    {

        if ($this->goup !== null) {
        //go back where you printed first
            $this->output("\r");
            $this->goUp($this->goup);
        }


        $timeFar = microtime(true) - $this->startedAt;

        $percent = $this->totalReadBytes / $this->total * 100;

        $percBar = round($percent, 0);
        $this->showProgressBar(self::FULL, self::EMPTY, $percBar);
        $str = $this->totalReadBytes . '/' . $this->total . ' bytes (' . (round($percent, 4)) . '%)  ';

        if ($percent === 0) {
            $eta = '?';
        } else {
            $eta = floor($this->readTimeTotal * 100 / $percent);
        }

        $this->output('  ' . $str);
        $this->output(PHP_EOL);

        $usedMem = memory_get_peak_usage(true);

        $this->output(
            $this->pad('Used Memory')
            . $this->round($usedMem, 0, 15)
            . ' / '
            . ($this->memoryLimit === '-1' ? ' not limited!' : $this->round($this->memoryLimit, 0, 15))
            . ' (' . $this->round($usedMem / $this->memoryLimit * 100) . '%) 
            ' . PHP_EOL

            . $this->pad('Running time') . $this->round($timeFar) . 's '
            . 'ETA:' . ($eta) . 's '
            . PHP_EOL

            . $this->pad('Read time')
            . $this->round($this->readTimeTotal) . 's '
            . '(' . $this->round($this->readTimeTotal / $timeFar * 100) . ' %) '
            . PHP_EOL

            . $this->pad('Anonymization time')
            . $this->round($this->anonymizationTotal) . 's '
            . '(' . $this->round($this->anonymizationTotal / $timeFar * 100) . ' %)      '
            . PHP_EOL . PHP_EOL
        );

        $goup = 7; //sum of above php_eol

        $output = '';
        foreach ($this->anonymizationTypes as $anonymizationType => $microtime) {
            if ($this->anonymizationTotal > 0) {
                $div = $microtime['time'] / $this->anonymizationTotal;
            } else {
                $div = 0;
            }
            $speed = 0;
            if ($microtime['size'] > 0 && $microtime['time'] > 0) {
                $speed = round(($microtime['size'] / 1024 / 1024) / $microtime['time'], 3);
            }

            $output .= $this->pad($anonymizationType) . $this->round($microtime['time']) . 's'
                . ' ('.str_pad((string)$speed, 6, ' ', STR_PAD_LEFT).' MB/s)'
                . ' (' . $this->round($div * 100) . ' %)  '
                . ' (x ' . $this->round($this->anonymizationTypes[$anonymizationType]['count'], 0) . ')'
                . ' (NULL: ' . $this->round($this->anonymizationTypes[$anonymizationType]['nulls'], 0) . ')'
                . PHP_EOL;
            $goup++;
        }
        $this->output($output);

        $this->goup = $goup;
    }

    private function pad($string): string
    {
        return str_pad($string, 30, ' ', STR_PAD_LEFT) . ':';
    }

    private function showProgressBar($full, $empty, $percentage): void
    {
        $progress = '';
        for ($i = 1; $i <= 100; $i++) {
            if ($i <= $percentage) {
                $progress .= $full;
            } else {
                $progress .= $empty;
            }
        }
        $this->output('  ' . $progress . ' ' . $percentage);
    }

    private function goUp($times = 1): void
    {
        $this->output(chr(27) . '[' . $times . 'A');
    }
}
