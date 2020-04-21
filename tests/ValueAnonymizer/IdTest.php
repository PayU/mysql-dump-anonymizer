<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Id;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Id $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Id($this->stringHashMock);
    }


    /** @dataProvider hashes
     * @param string $hash
     * @param string $expectedIdHash
     */
    public function testAnonymize($hash, $expectedIdHash): void
    {
        $this->stringHashMock->expects($this->once())->method('hashMe')->willReturn($hash);

        $actual = $this->sut->anonymize(new Value('\'2836143\'', '2836143', false), []);

        $this->assertSame($expectedIdHash, $actual->getRawValue());
    }

    public function hashes(): array
    {
        return [
            ['4282788', '\'4282788\''],
            ['5062063', '\'5062063\''],
            ['9617078', '\'9617078\''],
            ['00', '\'00\'']
        ];
    }

    public function testAnonymizeReturnSameValueIfUnquoted(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashMe');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }

}
