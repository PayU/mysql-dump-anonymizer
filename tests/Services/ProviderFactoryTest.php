<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Services;

use PayU\MysqlDumpAnonymizer\Anonymizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ProviderFactoryTest extends TestCase
{
    /**
     * @var Anonymizer|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}