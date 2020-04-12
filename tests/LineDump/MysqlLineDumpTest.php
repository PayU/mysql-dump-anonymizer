<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\LineDump;

use PayU\MysqlDumpAnonymizer\WriteDump\MysqlLineDumpInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MysqlLineDumpTest extends TestCase
{
    /**
     * @var MysqlLineDumpInterface|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}