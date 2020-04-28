<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Serialized;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SerializedTest extends TestCase
{
    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Serialized $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Serialized($this->stringHashMock);
    }

    public function testAnonymize(): void
    {
        $input = [
            'a'=>'next is unix line end:'."\n". 'end',
            'b'=>['next is single quote and double qoute\'"end']
        ];

        $serializedString = serialize($input);

        $value = new Value('raw', $serializedString, false);

        $this->stringHashMock->expects($this->exactly(2))->method('hashKeepFormat')
            ->willReturn('a', 'b');


        $expected = AnonymizedValue::fromUnescapedValue(serialize([
            'a'=>'a',
            'b'=>['b']
        ]));

        $actual = $this->sut->anonymize($value, []);

        $this->assertEquals($expected, $actual);
    }
}
