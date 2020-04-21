<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ReadDump;

use PayU\MysqlDumpAnonymizer\ReadDump\LineInfo;
use PHPUnit\Framework\TestCase;

class LineInfoTest extends TestCase
{

    /**
     * @var LineInfo
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new LineInfo(true, 't', ['col1','col2'], $this->iterable());
    }

    private function iterable()
    {
        $array = [['r1c1','r1c2'],['r2c1','r2c2']];
        yield from $array;
    }

    public function testIsInsert(): void
    {
        $actual = $this->sut->isInsert();
        $this->assertTrue($actual);
    }

    public function testTable(): void
    {
        $actual = $this->sut->getTable();
        $this->assertSame('t', $actual);
    }

    public function testColumns(): void
    {
        $actual = $this->sut->getColumns();
        $this->assertSame(['col1', 'col2'], $actual);
    }

    public function testParser(): void
    {
        $iterable = $this->sut->getValuesParser();

        $yeilds = [];
        foreach ($iterable as $yield) {
            $yeilds[] = $yield;
        }

        $this->assertIsIterable($iterable);
        $this->assertSame(['r1c1','r1c2'], $yeilds[0]);
        $this->assertSame(['r2c1','r2c2'], $yeilds[1]);
    }


}
