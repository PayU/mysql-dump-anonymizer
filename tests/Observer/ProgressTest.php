<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Observer;

use PayU\MysqlDumpAnonymizer\Observer\Progress;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ProgressTest extends TestCase
{
    /**
     * @var Progress|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}