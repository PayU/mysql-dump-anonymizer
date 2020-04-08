<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\LineDump;

use PayU\MysqlDumpAnonymizer\LineDump\MysqlLineDump;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MysqlLineDumpTest extends TestCase
{
    /**
     * @var MysqlLineDump|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}