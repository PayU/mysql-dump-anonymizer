<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Url;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @var Url
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Url();
    }

    public function testAnonymizeUrlWithScheme()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('hashMe')
            ->with('www.fashiondays.hu/generatetoken')
            ->willReturn('ubp.huhkosocgww.og/wwgwoockkokcg');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')
            ->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'http://www.fashiondays.hu/generatetoken\'', 'http://www.fashiondays.hu/generatetoken', false),
            [],
            $configMock
        );

        $this->assertSame('\'http://ubp.huhkosocgww.og/wwgwoockkokcg\'', $actual->getRawValue());
    }

    public function testAnonymizeUrlWithoutScheme()
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
        $hashStringMock->method('hashMe')
            ->with('www.alphabank.ro')
            ->willReturn('ubp.huhkosocg.og');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')
            ->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'www.alphabank.ro\'', 'www.alphabank.ro', false),
            [],
            $configMock
        );

        $this->assertSame('\'ubp.huhkosocg.og\'', $actual->getRawValue());
    }
}
