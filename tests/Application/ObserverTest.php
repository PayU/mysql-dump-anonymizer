<?php

namespace PayU\MysqlDumpAnonymizer\Tests\Application;

use PayU\MysqlDumpAnonymizer\Application\Observer;
use PayU\MysqlDumpAnonymizer\Application\Observer\ProcessObserverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ObserverTest extends TestCase
{

    /** @var ProcessObserverInterface|MockObject */
    private $observerMock;

    private Observer $sut;

    public function setUp(): void
    {
        $this->observerMock = $this->createMock(ProcessObserverInterface::class);
        $this->sut = new Observer();
        $this->sut->registerObserver($this->observerMock);
        parent::setUp();
    }

    /**
     * @dataProvider providerEventsMethods
     * @param string $event
     * @param string $method
     * @param null|int|string $data
     */
    public function testNotify($event, $method, $data = null): void
    {
        $this->observerMock->expects($this->once())->method($method);
        $this->sut->notify($event, $data);
    }

    public function providerEventsMethods(): array
    {
        return [
            [Observer::EVENT_START_READ, 'onStartReadLine'],
            [Observer::EVENT_END_READ, 'onFinishReadLine', 123],
            [Observer::EVENT_ANONYMIZATION_START, 'onAnonymizationStart', ['anonymizationType'=>'test','dataSize'=>10]],
            [Observer::EVENT_ANONYMIZATION_END, 'onAnonymizationFinish', 'data'],
            [Observer::EVENT_AFTER_LINE_PROCESSING, 'onAfterEachLineProcessing'],
            [Observer::EVENT_NOT_AN_INSERT, 'onNotAnInsertLine'],
            [Observer::EVENT_TRUNCATE, 'onTruncateLine'],
            [Observer::EVENT_INSERT_LINE_NO_ANONYMIZATION, 'onNoNeedForAnonymizationInsertLine'],
            [Observer::EVENT_BEGIN, 'onBegin'],
            [Observer::EVENT_NULL_VALUE , 'onNullValue', 'data'],
            [Observer::EVENT_NO_ANONYMIZATION , 'onNoAnonymization'],
            [Observer::EVENT_END , 'onEnd'],
        ];
    }
}
