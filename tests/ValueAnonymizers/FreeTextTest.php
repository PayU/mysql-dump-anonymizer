<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FreeTextTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private FreeText $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new FreeText($this->stringHashMock);
    }


    public function testAnonymize(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')
            ->willReturn('Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6');

        $actual = $this->sut->anonymize(
            new Value(
                '\'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5\'',
                'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5',
                false
            ),
            []
        );

        $this->assertSame('\'Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfUnquoted(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashKeepFormat');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }

    public function testAnonymizeSmall(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('Vkr.Hgcscgw');

        $actual = $this->sut->anonymize(
            new Value(
                '\'Sos.Nicolae\'',
                'Sos.Nicolae',
                false
            ),
            []
        );

        $this->assertSame('\'Vkr.Hgcscgw\'', $actual->getRawValue());
    }
}
