<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Phone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    /**
     * @var Phone
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Phone();
    }

    /** @dataProvider hashes */
    public function testAnonymize($hash, $expectedFinalHash)
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn($hash);

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(new Value('\'031 425 73 00\'', '031 425 73 00', false), [], $configMock);

        $this->assertSame($expectedFinalHash, $actual->getRawValue());
    }

    public function hashes()
    {
        return [
            ['0746.258.680', '\'0746.258.680\''],
            ['+59887842560', '\'+59887842560\''],
            ['0723250814', '\'0723250814\''],
        ];
    }
}
