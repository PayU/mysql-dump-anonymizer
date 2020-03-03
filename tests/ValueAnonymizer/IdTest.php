<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Id;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    /**
     * @var Id
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Id();
    }

    /** @dataProvider hashes */
    public function testAnonymize($hash, $expectedIdHash): void
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn($hash);

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(new Value('\'2836143\'', '2836143', false), [], $configMock);

        $this->assertSame($expectedIdHash, $actual->getRawValue());
    }

    public function hashes()
    {
        return [
            ['4282788', '\'4282788\''],
            ['5062063', '\'5062063\''],
            ['9617078', '\'9617078\''],
        ];
    }
}
