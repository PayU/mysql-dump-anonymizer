<?php

declare(strict_types=1);


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

    public function testAnonymize()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('74eca695');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(new Value('\'RO427320\'', 'RO427320', false), [], $configMock);

        $this->assertSame('\'74eca695\'', $actual->getRawValue());
    }
}
