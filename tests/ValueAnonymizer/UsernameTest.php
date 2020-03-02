<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Username;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UsernameTest extends TestCase
{
    /**
     * @var Username
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Username();
    }

    public function testAnonymizeUsernameWithLengthBiggerThan12()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('hashMe')->willReturn('cgodertgy.dndem');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'anastasia.matei\'', 'anastasia.matei', false),
            [],
            $configMock
        );

        $this->assertSame('\'cgodertgy.dndem\'', $actual->getRawValue());
    }

    public function testAnonymizeUsernameWithLengthSmallerThan12()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('sha256')->willReturn('eee.fgdjf');
        $hashStringMock->method('hashMe')->with('eee.fgdjf')->willReturn('cgo.dndem');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'ana.matei\'', 'ana.matei', false),
            [],
            $configMock
        );

        $this->assertSame('\'cgo.dndem\'', $actual->getRawValue());
    }
}
