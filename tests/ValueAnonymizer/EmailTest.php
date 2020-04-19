<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{

    public function testAnonymize(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('djbtxh@kwkogksok.ok');

        /** @var \PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $sut = new Email($configMock);

        $actual = $sut->anonymize(
            new Value('\'abyhfi@ijuyhoung.ro\'', 'abyhfi@ijuyhoung.ro', false), []
        );

        $this->assertSame('\'djbtxh@kwkogksok.ok\'', $actual->getRawValue());
    }
}
