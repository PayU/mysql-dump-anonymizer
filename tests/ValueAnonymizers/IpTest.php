<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Ip;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IpTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Ip $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Ip($this->stringHashMock);
    }

    /** @dataProvider hashes
     * @param string $expectedIp
     */
    public function testAnonymize($expectedIp): void
    {
        $this->stringHashMock->expects($this->once())->method('hashIpAddressString')->willReturn($expectedIp);

        $actual = $this->sut->anonymize(new Value('\'test\'', 'test', false), []);

        $this->assertSame('\''.$expectedIp.'\'', $actual->getRawValue());
    }

    public function hashes(): array
    {
        return [
            ['0.0.0.0'],
            ['255.255.255.255'],
            ['47.242.47.47'],
            ['50.153.56.237'],
            ['0.0.0.0'],
            ['0.238.238.238'],
            ['0.0.0.238'],
            ['170.187.136.85']

        ];
    }
}
