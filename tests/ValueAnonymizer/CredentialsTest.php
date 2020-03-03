<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Credentials;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /**
     * @var Credentials
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Credentials();
    }

    public function testAnonymize(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('pass~?i%%#e');

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'afdg$%^&@w\'', 'afdg$%^&@w', false),
            [],
            $configMock
        );

        $this->assertSame('\'pass~?i%%#e\'', $actual->getRawValue());
    }
}
