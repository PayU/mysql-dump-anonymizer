<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Provider;

use PayU\MysqlDumpAnonymizer\Provider\AnonymizationProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AnonymizationProviderTest extends TestCase
{
    /**
     * @var AnonymizationProvider|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}