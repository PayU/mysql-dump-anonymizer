<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Helper;

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
        $expected = 'Nxz. Ymfas 9298 KW-WG|C&WW%g@?OKSWOKKW ';

        $actual = $this->sut->hashMe($word);
        $this->assertSame($expected, $actual);
    }

    public function testUnixLines(): void
    {

        $word = 'newline'."\n". 'end';
        $expected = 'uqhxkzp'."\n".'wsk';

        $actual = $this->sut->hashMe($word);

        $this->assertSame($expected, $actual);
    }

    public function testWindowsLines(): void
    {

        $word = 'newline'."\r\n". 'end';
        $expected = 'qjkeiou'."\r\n".'wks';

        $actual = $this->sut->hashMe($word);

        $this->assertSame($expected, $actual);
    }

    public function testTabs(): void
    {

        $word = 'newline'."\t". 'end';
        $expected = 'slmeavk'."\t".'qmz';

        $actual = $this->sut->hashMe($word);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider manyProvider
     * @param string $input
     * @param string $expected
     */
    public function testMany($input, $expected) {

        $actual = $this->sut->hashMe($input);
        $this->assertSame($expected, $actual);

    }

    public function manyProvider()
    {
        return [
            ['/admin/log_monitor.php', '/odfeb/ymt_wecgccw.gsw']
        ];
    }
}
