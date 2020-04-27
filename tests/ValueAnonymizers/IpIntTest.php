<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
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
    public function testAnonymize($expectedIp): void
    {
        $this->stringHashMock->expects($this->once())->method('hashIpAddressString')->willReturn(long2ip((int)$expectedIp));

        $actual = $this->sut->anonymize(new Value('\''.$expectedIp.'\'', $expectedIp, false), []);

        $this->assertSame($expectedIp, $actual->getRawValue());
    }

    public function hashes(): array
    {
        return [
            ['0'], //'0.0.0.0'
            [(PHP_INT_SIZE === 4 ? '-1':'4294967295')], //'255.255.255.255'
            ['804400943'], //'47.242.47.47'
            ['848902381'], //'50.153.56.237'
            ['0'], //'0.0.0.0'
            ['15658734'], //'0.238.238.238'
            ['238'], //'0.0.0.238'
            [(PHP_INT_SIZE === 4 ? '-1430550443' : '2864416853')]
        ];
    }
}
