<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Json;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Json $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Json($this->stringHashMock);
    }


    public function testAnonymize(): void
    {

        $a = ['test1' => 'str. hello nr.1', ['test2' => 'John', 'integer' => 123, 'float' => 12.999999999]];
        $string = json_encode($a);
        $av = new Value('\'' . addslashes($string) . '\'', $string, false);

        $expectedJson = [
            'test1' => 'anonimized(strheelo)',
            0 => [
                'test2' => 'anony"mized(John)',
                'integer' => 123, // expected int, StringHash returns string !
                'float' => 12.999999999
            ]
        ];

        $expected = AnonymizedValue::fromUnescapedValue(json_encode($expectedJson));

        $this->stringHashMock->expects($this->exactly(4))->method('hashKeepFormat')->willReturn(
            $expectedJson['test1'],
            $expectedJson[0]['test2'],
            (string)$expectedJson[0]['integer'],
            (string)$expectedJson[0]['float']
        );

        $actual = $this->sut->anonymize($av, []);

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected->getRawValue(), $actual->getRawValue());
    }
}
