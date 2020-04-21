<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\IpInt;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IpIntTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private IpInt $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);

        $this->sut = new IpInt($this->stringHashMock);
    }


    /** @dataProvider hashes
     * @param string $hash
     * @param string $expectedIp
     */
    public function testAnonymize($hash, $expectedIp): void
    {
        $this->stringHashMock->expects($this->once())->method('sha256')->willReturn($hash);

        $actual = $this->sut->anonymize(new Value('\'test\'', 'test', false), []);

        $this->assertSame($expectedIp, $actual->getRawValue());
    }

    public function hashes(): array
    {
        return [
            ['0000000000000000000000000000000000000000000000000000000000000000', '0'], //'0.0.0.0'
            ['FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF',
                (PHP_INT_SIZE === 4 ? '-1':'4294967295')
            ], //'255.255.255.255'
            ['F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2F2', '804400943'], //'47.242.47.47'
            ['fe99332f3f2d9093148defa03fa43728feaff00edaa23303fa4372837ddabcde', '848902381'], //'50.153.56.237'
            ['000000000000000000000000000000000000000000000000000000000000000e', '0'], //'0.0.0.0'
            ['ee00000000000000000000000000000000000000000000000000000000eeeeee', '15658734'], //'0.238.238.238'
            ['0000000000000000000000000000000000000000000000000000000000eeeeee', '238'], //'0.0.0.238'
            ['aabbccddeeff00112233445566778899aabbccddeeff00112233445566778899', (PHP_INT_SIZE === 4 ? '-1430550443' : '2864416853')]
        ];
    }
}
