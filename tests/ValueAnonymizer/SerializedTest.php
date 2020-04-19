<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Serialized;
use PHPUnit\Framework\MockObject\MockObject;

class SerializedTest extends AbstractValueAnonymizerMocks
{

    public function testAnonymize(): void
    {
        $input = [
            'a'=>'next is unix line end:'."\n". 'end',
            'b'=>['next is single quote and double qoute\'"end']
        ];

        $serializedString = serialize($input);

        $value = new Value('raw', $serializedString, false);

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock([
            'a',
            'b',
        ]);

        $expected = new AnonymizedValue(EscapeString::escape(serialize([
            'a'=>'a',
            'b'=>['b']
        ])));


        $actual = (new Serialized($configMock))->anonymize($value, []);

        $this->assertEquals($expected, $actual);
    }
}
