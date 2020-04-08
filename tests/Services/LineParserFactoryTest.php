<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Services;

use PayU\MysqlDumpAnonymizer\Services\LineParserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LineParserFactoryTest extends TestCase
{
    /**
     * @var LineParserFactory|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}