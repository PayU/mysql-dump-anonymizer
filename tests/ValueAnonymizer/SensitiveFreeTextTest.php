<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\SensitiveFreeText;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SensitiveFreeTextTest extends TestCase
{

    public function testAnonymize(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('XGV Zkmtao Wggkcckg WOO');

        /** @var \PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = (new SensitiveFreeText($configMock))->anonymize(
            new Value('\'OLX Online Services SRL\'', 'OLX Online Services SRL', false), []
        );

        $this->assertSame('\'XGV Zkmtao Wggkcckg WOO\'', $actual->getRawValue());
    }
}
