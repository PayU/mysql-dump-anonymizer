<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FreeText;
use PHPUnit\Framework\MockObject\MockObject;

class FreeTextTest extends AbstractValueAnonymizerMocks
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
        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock(['Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6']);

        $actual = $this->sut->anonymize(
            new Value('\'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5\'', 'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5', false),
            [],
            $configMock
        );

        $this->assertSame('\'Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6\'', $actual->getRawValue());
    }
}
