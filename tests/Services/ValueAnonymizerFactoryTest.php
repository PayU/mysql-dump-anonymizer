<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Services;

use PayU\MysqlDumpAnonymizer\ConfigReader\ValueAnonymizerFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ValueAnonymizerFactoryTest extends TestCase
{
    /**
     * @var ValueAnonymizerFactory|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}