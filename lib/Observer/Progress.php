<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Observer;

class Progress implements ProcessObserverInterface {

    private $total = 0;
    private $startedAt;
    private $startReadLine;

    private $readTimeTotal = 0;

    private $anonymizationTypes = [];

    private $anonymizationTypeStart = 0;

    private $truncate= 0;

    private $notAnInsert = 0;

    private $insertLineNoNeedForAnonymization = 0;

    private $noAnonymization = 0;


    public function onBegin($estimatedTotalInputLength): void
    {
        $this->total = $estimatedTotalInputLength;
        $this->startedAt = microtime(true);
    }

    public function onStartReadLine(): void
    {
        $this->startReadLine = microtime(true);
    }

    public function onFinishReadLine(int $lineLength): void
    {
        $this->readTimeTotal += (microtime(true) - $this->startReadLine);
    }

    public function onNullValue(string $anonymizationType): void
    {
        $this->getAnonymizationType($anonymizationType)['nulls']++;
    }

    public function onAnonymizationStart(string $anonymizationType): void
    {
        $this->getAnonymizationType($anonymizationType)['count']++;
        $this->anonymizationTypeStart = microtime(1);
    }

    public function onAnonymizationFinish(string $anonymizationType): void
    {
        $this->getAnonymizationType($anonymizationType)['time'] += ($this->anonymizationTypeStart - microtime(1));
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
        //show everything
    }



    private function &getAnonymizationType($key) : array {
        if (!array_key_exists($key, $this->anonymizationTypes)) {
            $this->anonymizationTypes[$key] = [
                'nulls' => 0,
                'time' => 0,
                'count' => 0,
            ];
        }

        return $this->anonymizationTypes[$key];

    }


}

