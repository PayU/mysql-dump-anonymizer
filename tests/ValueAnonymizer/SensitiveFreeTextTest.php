<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\SensitiveFreeText;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SensitiveFreeTextTest extends TestCase
{
    /**
     * @var SensitiveFreeText
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new SensitiveFreeText();
    }

    public function testAnonymize()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('XGV Zkmtao Wggkcckg WOO');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'OLX Online Services SRL\'', 'OLX Online Services SRL', false),
            [],
            $configMock
        );

        $this->assertSame('\'XGV Zkmtao Wggkcckg WOO\'', $actual->getRawValue());
    }
}
