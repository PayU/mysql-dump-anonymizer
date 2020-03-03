<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Id;

class IdTest extends AbstractValueAnonymizerMocks
{
    /**
     * @var Id
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Id();
    }

    /** @dataProvider hashes
     * @param string $hash
     * @param string $expectedIdHash
     */
    public function testAnonymize($hash, $expectedIdHash): void
    {
        $configMock = $this->anonymizerConfigMock([$hash]);

        $actual = $this->sut->anonymize(new Value('\'2836143\'', '2836143', false), [], $configMock);

        $this->assertSame($expectedIdHash, $actual->getRawValue());
    }

    public function hashes()
    {
        return [
            ['4282788', '\'4282788\''],
            ['5062063', '\'5062063\''],
            ['9617078', '\'9617078\''],
        ];
    }
}
