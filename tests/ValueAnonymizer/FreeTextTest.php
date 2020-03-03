<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FreeText;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FreeTextTest extends TestCase
{
    /**
     * @var FreeText
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new FreeText();
    }

    public function testAnonymize(): void
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn('Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6');

        /** @var Config|MockObject $configMock */
        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = $this->sut->anonymize(
            new Value('\'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5\'', 'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5', false),
            [],
            $configMock
        );

        $this->assertSame('\'Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6\'', $actual->getRawValue());
    }
}
