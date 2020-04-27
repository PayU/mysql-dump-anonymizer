<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Email $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Email($this->stringHashMock);
    }


    public function testAnonymize(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('djbtxh@kwkogksok.ok');

        $actual = $this->sut->anonymize(
            new Value('\'abyhfi@ijuyhoung.ro\'', 'abyhfi@ijuyhoung.ro', false),
            []
        );

        $this->assertSame('\'djbtxh@kwkogksok.ok\'', $actual->getRawValue());
    }


    public function testAnonymizeReturnSameValueIfUnquoted(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashKeepFormat');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }
}
