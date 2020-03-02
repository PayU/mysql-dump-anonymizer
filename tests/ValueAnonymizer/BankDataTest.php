<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\BankData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BankDataTest extends TestCase
{
    /**
     * @var BankData
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new BankData();
    }

    public function testAnonymize()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('hashMe')->willReturn('84fc');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(new Value('\'BCRL\'', 'BCRL', false), [], $configMock);

        $this->assertSame('\'84fc\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfExpression()
    {
        $actual = $this->sut->anonymize(new Value('\'expression\'', 'expression', true), [], new Config());

        $this->assertSame('\'expression\'', $actual->getRawValue());
    }
}
