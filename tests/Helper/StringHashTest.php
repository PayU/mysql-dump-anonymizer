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

    /**
     * @dataProvider manyProvider
     * @param string $input
     * @param string $expected
     */
    public function testMany($input, $expected)
    {

        $actual = $this->sut->hashMe($input);
        $this->assertSame($expected, $actual);

    }

    public function manyProvider()
    {
        return [
            ['Str. Hello 1213 AA-AA|A&AA%☻AAAAAAAA ', 'Nxz. Ymfas 9298 KW-WG|C&WW%g@?OKSWOKKW '],
            ['/admin/log_monitor.php', '/odfeb/ymt_wecgccw.gsw'],
            ['newline' . "\n" . 'end', 'uqhxkzp' . "\n" . 'wsk'],
            ['newline' . "\r\n" . 'end', 'qjkeiou' . "\r\n" . 'wks'],
            ['newline' . "\t" . 'end', 'slmeavk' . "\t" . 'qmz'],
        ];
    }
}
