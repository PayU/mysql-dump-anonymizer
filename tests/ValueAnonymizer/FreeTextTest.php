<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FreeText;
use PHPUnit\Framework\MockObject\MockObject;

class FreeTextTest extends AbstractValueAnonymizerMocks
{

    public function testAnonymize(): void
    {
        /** @var \PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock(['Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6']);
        $sut = new FreeText($configMock);

        $actual = $sut->anonymize(
            new Value('\'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5\'', 'Sos.Nicolae Titulescu,nr4-8,Cladirea America House-Aripa de Vest,Etaj5', false), []
        );

        $this->assertSame('\'Vkr.Hgcscgw Swgokwkgs,ks3-8,Gsgosowg Kwkovkr Hgcsc-Gwswg ok Wkgs,Ksgs6\'', $actual->getRawValue());
    }
}
