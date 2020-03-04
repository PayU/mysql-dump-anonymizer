<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @var Email
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Email();
    }

    public function testAnonymize(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('djbtxh@kwkogksok.ok');

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'abyhfi@ijuyhoung.ro\'', 'abyhfi@ijuyhoung.ro', false), []
        );

        $this->assertSame('\'djbtxh@kwkogksok.ok\'', $actual->getRawValue());
    }
}
