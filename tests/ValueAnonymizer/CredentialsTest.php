<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Credentials;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Credentials $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Credentials($this->stringHashMock);
    }

    public function testAnonymize(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashMe')->willReturn('pass~?i%%#e');

        $actual = $this->sut->anonymize(
            new Value('\'afdg$%^&@w\'', 'afdg$%^&@w', false), []
        );

        $this->assertSame('\'pass~?i%%#e\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfExpression(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashMe');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }


}
