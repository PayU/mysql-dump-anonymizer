<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Json;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PHPUnit\Framework\MockObject\MockObject;

final class JsonTest extends AbstractValueAnonymizerMocks
{

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

        $expected = new AnonymizedValue(
            EscapeString::escape(json_encode($expectedJson))
        );

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock([
            $expectedJson['test1'],
            $expectedJson[0]['test2'],
            (string)$expectedJson[0]['integer'],
            (string)$expectedJson[0]['float']
        ]);

        $sut = new Json($configMock);

        $actual = $sut->anonymize($av, []);

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected->getRawValue(), $actual->getRawValue());
    }
}
