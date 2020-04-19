<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Url;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{

    public function testAnonymizeUrlWithScheme(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
        $hashStringMock->method('hashMe')
            ->with('www.fashiondays.hu/generatetoken')
            ->willReturn('ubp.huhkosocgww.og/wwgwoockkokcg');

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')
            ->willReturn($hashStringMock);

        $actual = (new Url($configMock))->anonymize(
            new Value('\'http://www.fashiondays.hu/generatetoken\'', 'http://www.fashiondays.hu/generatetoken', false), []
        );

        $this->assertSame('\'http://ubp.huhkosocgww.og/wwgwoockkokcg\'', $actual->getRawValue());
    }

    public function testAnonymizeUrlWithoutScheme(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
        $hashStringMock->method('hashMe')
            ->with('www.alphabank.ro')
            ->willReturn('ubp.huhkosocg.og');

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')
            ->willReturn($hashStringMock);

        $actual = (new Url($configMock))->anonymize(
            new Value('\'www.alphabank.ro\'', 'www.alphabank.ro', false), []
        );

        $this->assertSame('\'ubp.huhkosocg.og\'', $actual->getRawValue());
    }
}
