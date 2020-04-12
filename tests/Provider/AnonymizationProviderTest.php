<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Provider;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AnonymizationProviderTest extends TestCase
{
    /**
     * @var \PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProvider|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}