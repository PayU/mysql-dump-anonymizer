<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Credentials;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /**
     * @var Credentials
     */
    private $sut;


    public function testAnonymize(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('pass~?i%%#e');

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $sut = new Credentials($configMock);

        $actual = $sut->anonymize(
            new Value('\'afdg$%^&@w\'', 'afdg$%^&@w', false), []
        );

        $this->assertSame('\'pass~?i%%#e\'', $actual->getRawValue());
    }
}
