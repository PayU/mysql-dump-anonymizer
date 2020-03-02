<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\DocumentData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DocumentDataTest extends TestCase
{
    /**
     * @var DocumentData
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new DocumentData();
    }

    /** @dataProvider hashes */
    public function testAnonymize($hash, $expectedDocumentDataHash)
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('hashMe')->willReturn($hash);

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(new Value('\'RO427320\'', 'RO427320', false), [], $configMock);

        $this->assertSame($expectedDocumentDataHash, $actual->getRawValue());
    }

    public function hashes()
    {
        return [
            ['74eca695', '\'74eca695\''],
            ['78f92788', '\'78f92788\''],
            ['57ea99ae3', '\'57ea99ae3\''],
        ];
    }
}
