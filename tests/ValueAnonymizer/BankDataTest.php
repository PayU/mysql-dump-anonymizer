<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\BankData;

class BankDataTest extends AbstractValueAnonymizerMocks
{


    public function testAnonymize(): void
    {
        $configMock = $this->anonymizerConfigMock(['84fc']);
        $sut = new BankData($configMock);

        $actual = $sut->anonymize(new Value('\'BCRL\'', 'BCRL', false), []);

        $this->assertSame('\'84fc\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfExpression(): void
    {
        $configMock = $this->anonymizerConfigMock(null);
        $sut = new BankData($configMock);

        $actual = $sut->anonymize(new Value('\'expression\'', 'expression', true), []);

        $this->assertSame('\'expression\'', $actual->getRawValue());
    }
}
