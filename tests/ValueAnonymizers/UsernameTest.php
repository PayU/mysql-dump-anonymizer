<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Username;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UsernameTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Username $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Username($this->stringHashMock);
    }


    public function testAnonymizeUsernameWithLengthBiggerThan12(): void
    {

        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('cgod.dnde.jfuwlfntol');
        $this->stringHashMock->expects($this->never())->method('sha256');

        $actual = $this->sut->anonymize(
            new Value('\'some.name.longthan12\'', 'some.name.longthan12', false), []
        );

        $this->assertSame('\'cgod.dnde.jfuwlfntol\'', $actual->getRawValue());
    }

    public function testAnonymizeUsernameWithLengthSmallerThan12(): void
    {
        $this->stringHashMock->expects($this->once())->method('sha256')->with('small.name')->willReturn(
            '1234567890abcdefghij1234567890abcdefghij1234567890abcdefghijffff'
        );

        $this->stringHashMock->expects($this->once())
            ->method('hashKeepFormat')
            ->with('1234567890ab')
            ->willReturn('cgodd.dnde');

        $actual = $this->sut->anonymize(
            new Value('\'small.name\'', 'small.name', false), []
        );

        $this->assertSame('\'cgodd.dnde\'', $actual->getRawValue());
    }

    public function testAnonymizeUsernameExpr(): void
    {

        $this->stringHashMock->expects($this->never())->method('hashKeepFormat');
        $this->stringHashMock->expects($this->never())->method('sha256');

        $actual = $this->sut->anonymize(
            new Value('NULL', 'NULL', true), []
        );

        $this->assertSame('NULL', $actual->getRawValue());
    }
}
