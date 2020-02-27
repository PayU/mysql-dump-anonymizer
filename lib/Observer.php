<?php

namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Observer\ProcessObserverInterface;

class Observer
{
    public const EVENT_START_READ = 1;
    public const EVENT_END_READ = 2;
    public const EVENT_ANONYMIZATION_START = 3;
    public const EVENT_ANONYMIZATION_END = 4;
    public const EVENT_AFTER_LINE_PROCESSING = 5;
    public const EVENT_NOT_AN_INSERT = 6;
    public const EVENT_TRUNCATE = 7;
    public const EVENT_INSERT_LINE_NO_ANONYMIZATION = 8;
    public const EVENT_BEGIN = 9;
    public const EVENT_NULL_VALUE = 10;
    public const EVENT_NO_ANONYMIZATION = 11;

    /** @var ProcessObserverInterface[] */
    private $observers;

    public function __construct()
    {
    }

    public function registerObserver(ProcessObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify($event, $data = null) : void
    {
        foreach ($this->observers as $observer) {
            switch ($event) {
                case self::EVENT_START_READ:
                    $observer->onStartReadLine();
                    break;
                case self::EVENT_END_READ:
                    $observer->onFinishReadLine($data);
                    break;
                case self::EVENT_ANONYMIZATION_START:
                    $observer->onAnonymizationStart($data);
                    break;
                case self::EVENT_ANONYMIZATION_END:
                    $observer->onAnonymizationFinish($data);
                    break;
                case self::EVENT_AFTER_LINE_PROCESSING:
                    $observer->onAfterEachLineProcessing();
                    break;
                case self::EVENT_NOT_AN_INSERT:
                    $observer->onNotAnInsertLine();
                    break;
                case self::EVENT_TRUNCATE:
                    $observer->onTruncateLine();
                    break;
                case self::EVENT_INSERT_LINE_NO_ANONYMIZATION:
                    $observer->onNoNeedForAnonymizationInsertLine();
                    break;
                case self::EVENT_BEGIN:
                    $observer->onBegin($data);
                    break;
                case self::EVENT_NULL_VALUE:
                    $observer->onNullValue($data);
                    break;
                case self::EVENT_NO_ANONYMIZATION:
                    $observer->onNoAnonymization();
                    break;
            }
        }
    }


}