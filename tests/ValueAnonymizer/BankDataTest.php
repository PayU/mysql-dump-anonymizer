<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\BankData;

class BankDataTest extends AbstractValueAnonymizerMocks
{
    /**
     * @var BankData
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new BankData();
    }

    public function testAnonymize(): void
    {
        $configMock = $this->anonymizerConfigMock(['84fc']);

        $actual = $this->sut->anonymize(new Value('\'BCRL\'', 'BCRL', false), [], $configMock);

        $this->assertSame('\'84fc\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfExpression(): void
    {
        $actual = $this->sut->anonymize(new Value('\'expression\'', 'expression', true), [], new Config());

        $this->assertSame('\'expression\'', $actual->getRawValue());
    }
}
