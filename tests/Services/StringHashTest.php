<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Services;

use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class StringHashTest extends TestCase
{
    /**
     * @var StringHash|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new StringHash('test-salt');


    }

    public function testme(): void
    {

        $word = 'Str. Hello 1213 AA-AA|A&AA%☻AAAAAAAA ';
        for ($i=1;$i<=2;$i++) {
            $word .= $word;
        }

        $expected = 'Gbz. Dciyy 5168 AO-CW|S&SW%s"[GKCWGKWC '
            .'Ooc. Cokwk 0000 GB-ZD|C&IY%y[;AOCWSSWS '
            .'Gkc. Wgkwc 2864 OO-CC|O&KW%k)"GBZDCIYY '
            .'Aoc. Wssws 6424 GK-CW|G&KW%c=*OOCCOKWK ';

        $actual = $this->sut->hashMe($word);

        $this->assertSame($expected, $actual);

    }


}