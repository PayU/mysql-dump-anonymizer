<?php
declare(strict_types=1);

use PayU\MysqlDumpAnonymizer\Services\LineParser\MySqlDumpLineParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MySqlDumpLineParserTest extends TestCase
{
    /**
     * @var MySqlDumpLineParser|MockObject
     */
    private $sut;

    public function setUp() : void
    {
        parent::setUp();

        $this->sut = new MySqlDumpLineParser();
    }

    public function testActionIndexSuccess(): void
    {
        /** @noinspection SqlResolve */
        $actual = $this->sut->lineInfo('INSERT INTO `table` (`a`, `b`) VALUES (1,2), (3,4)');

        $this->assertSame('table', $actual->getTable());
        $this->assertSame(['a','b'], $actual->getColumns());
    }
}
