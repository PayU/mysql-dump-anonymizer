<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterfaceSha256;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class StringHashSha256Test extends TestCase
{
    /**
     * @var StringHashInterfaceSha256|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $obj         = new StringHashInterfaceSha256();
        $refObject   = new ReflectionObject( $obj );
        $refProperty = $refObject->getProperty( 'salt' );
        $refProperty->setAccessible( true );
        $refProperty->setValue($obj, 'test-salt');

        $this->sut = $obj;
    }

    /**
     * @dataProvider manyProvider
     * @param string $input
     * @param string $expected
     */
    public function testMany($input, $expected): void
    {

        $actual = $this->sut->hashMe($input);
        $this->assertSame($expected, $actual);

    }

    public function manyProvider(): array
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
