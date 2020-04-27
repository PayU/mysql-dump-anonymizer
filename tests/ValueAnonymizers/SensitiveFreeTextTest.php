<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\SensitiveFreeText;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SensitiveFreeTextTest extends TestCase
{
    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private SensitiveFreeText $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new SensitiveFreeText($this->stringHashMock);
    }

    public function testAnonymize(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('XGV Zkmtao Wggkcckg WOO');

        $actual = $this->sut->anonymize(
            new Value('\'OLX Online Services SRL\'', 'OLX Online Services SRL', false), []
        );

        $this->assertSame('\'XGV Zkmtao Wggkcckg WOO\'', $actual->getRawValue());
    }
}
