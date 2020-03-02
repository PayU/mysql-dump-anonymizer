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

    /** @dataProvider hashes */
    public function testAnonymize($hash, $expectedBankDataHash)
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('hashMe')->willReturn($hash);

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(new Value('\'BCRL\'', 'BCRL', false), [], $configMock);

        $this->assertSame($expectedBankDataHash, $actual->getRawValue());
    }

    public function hashes()
    {
        return [
            ['84fc', '\'84fc\''],
            ['f2c6', '\'f2c6\''],
            ['22a160c2', '\'22a160c2\''],
        ];
    }
}
