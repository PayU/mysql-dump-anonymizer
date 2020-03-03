<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Serialized;
use PHPUnit\Framework\MockObject\MockObject;

class SerializedTest extends AbstractValueAnonymizerMocks
{
    /**
     * @var Serialized
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new Serialized();
    }

    public function testAnonymize()
    {
        $input = [
            'a'=>'next is unix line end:'."\n". 'end',
            'b'=>['next is single quote and double qoute\'"end']
        ];

        $serializedString = serialize($input);

        $value = new Value('raw', $serializedString, false);

        /** @var Config|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock([
            'a',
            'b',
        ]);

        $expected = new AnonymizedValue(EscapeString::escape(serialize([
            'a'=>'a',
            'b'=>['b']
        ])));


        $actual = $this->sut->anonymize($value, [], $configMock);

        $this->assertEquals($expected, $actual);
    }
}
