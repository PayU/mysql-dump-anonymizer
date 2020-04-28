<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\DocumentData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DocumentDataTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private DocumentData $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new DocumentData($this->stringHashMock);
    }


    public function testAnonymize(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('74eca695');

        $actual = $this->sut->anonymize(new Value('\'RO427320\'', 'RO427320', false), []);

        $this->assertSame('\'74eca695\'', $actual->getRawValue());
    }


    public function testAnonymizeReturnSameValueIfExpression(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashKeepFormat');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }
}
