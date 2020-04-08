<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests;

use PayU\MysqlDumpAnonymizer\Observer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ObserverTest extends TestCase
{
    /**
     * @var Observer|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}