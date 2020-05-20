<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Application\Observer;

interface ProcessObserverInterface
{
    public function onBegin($estimatedTotalInputLength) : void;

    public function onStartReadLine(): void;

    public function onFinishReadLine(int $lineLength): void;

    public function onNullValue(string $anonymizationType): void;

    public function onAnonymizationStart(string $anonymizationType, int $dataSize): void;

    public function onAnonymizationFinish(string $anonymizationType): void;

    public function onNotAnInsertLine(): void;

    public function onTruncateLine(): void;

    public function onNoNeedForAnonymizationInsertLine(): void;

    public function onAfterEachLineProcessing(): void;

    public function onNoAnonymization() : void;

    public function onEnd() : void;
}
