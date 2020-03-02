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
            ['Str. Hello 1213 AA-AA|A&AA%☻AAAAAAAA ', 'Oss. Cwogo 0288 KW-WO|C&CS%o!!CGKSKGCC '],
            ['/admin/log_monitor.php', '/sckgs/wco_gowwowo.wsw'],
            ['newline' . "\n" . 'end', 'gwscksc'."\n".'wsk'],
            ['newline' . "\r\n" . 'end', 'sowwgko'."\r\n".'gkw'],
            ['newline' . "\t" . 'end', 'ocsswcc'."\t".'wko'],
        ];
    }
}
