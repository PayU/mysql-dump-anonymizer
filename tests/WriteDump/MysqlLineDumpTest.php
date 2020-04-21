<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\WriteDump;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\WriteDump\MysqlLineDump;
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
        $this->sut = new MysqlLineDump();
    }

    public function testDump(): void
    {
        /** @noinspection SqlResolve */
        $expected = "INSERT INTO `t` (`c1`, `c2`) VALUES (NULL, 'r1c2'), ('r2c1', 'r2c2');".PHP_EOL;

        $actual = $this->sut->rebuildInsertLine('t', ['c1', 'c2'], [[
            AnonymizedValue::fromRawValue('NULL'),
            AnonymizedValue::fromUnescapedValue('r1c2'),
        ], [
            AnonymizedValue::fromUnescapedValue('r2c1'),
            AnonymizedValue::fromUnescapedValue('r2c2'),
        ]]);

        $this->assertSame($expected, $actual);

    }

}