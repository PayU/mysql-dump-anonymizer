<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\BankData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BankDataTest extends TestCase
{
    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private BankData $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new BankData($this->stringHashMock);
    }


    public function testAnonymize(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashMe')->willReturn('84fc');

        $actual = $this->sut->anonymize(new Value('\'BCRL\'', 'BCRL', false), []);

        $this->assertSame('\'84fc\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfExpression(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashMe');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }
}
