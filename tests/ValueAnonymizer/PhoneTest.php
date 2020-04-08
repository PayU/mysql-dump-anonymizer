<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Phone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{

    /** @dataProvider hashes
     * @param string $hash
     * @param string $expectedFinalHash
     */
    public function testAnonymize($hash, $expectedFinalHash): void
    {
        $hashStringMock = $this->getMockBuilder(StringHash::class)->getMock();
        $hashStringMock->method('hashMe')->willReturn($hash);

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $actual = (new Phone($configMock))->anonymize(new Value('\'031 425 73 00\'', '031 425 73 00', false), []);

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
