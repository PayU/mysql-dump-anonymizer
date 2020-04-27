<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Phone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{


    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Phone $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Phone($this->stringHashMock);
    }

    /** @dataProvider hashes
     * @param string $hash
     * @param string $expectedFinalHash
     */
    public function testAnonymize($hash, $expectedFinalHash): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn($hash);

        $actual = $this->sut->anonymize(new Value('\'031 425 73 00\'', '031 425 73 00', false), []);

        $this->assertSame($expectedFinalHash, $actual->getRawValue());
    }

    public function hashes(): array
    {
        return [
            ['0746.258.680', '\'0746.258.680\''],
            ['+59887842560', '\'+59887842560\''],
            ['0723250814', '\'0723250814\''],
        ];
    }
}
