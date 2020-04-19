<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Ip;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IpTest extends TestCase
{

    /** @dataProvider hashes
     * @param string $hash
     * @param string $expectedIp
     */
    public function testAnonymize($hash, $expectedIp): void
    {
        $hashStringMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
        $hashStringMock->method('sha256')->willReturn($hash);

        /** @var \PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface|MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $configMock->method('getHashStringHelper')->willReturn($hashStringMock);

        $sut = new Ip($configMock);

        $actual = $sut->anonymize(new Value('\'test\'', 'test', false), []);

        $this->assertSame('\''.$expectedIp.'\'', $actual->getRawValue());
    }

    public function hashes(): array
    {
        return [
            ['0000000000000000000000000000000000000000000000000000000000000000', '0.0.0.0'],
            ['FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF', '255.255.255.255'],
            ['F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2', '47.242.47.47'],
            ['fe99332f3f2d9093148defa03fa43728feaff00edaa23303fa4372837ddabcde', '50.153.56.237'],
            ['000000000000000000000000000000000000000000000000000000000000000e', '0.0.0.0'],
            ['ee00000000000000000000000000000000000000000000000000000000eeeeee', '0.238.238.238'],
            ['0000000000000000000000000000000000000000000000000000000000eeeeee', '0.0.0.238'],
            ['aabbccddeeff00112233445566778899aabbccddeeff00112233445566778899', '170.187.136.85']

        ];
    }
}
