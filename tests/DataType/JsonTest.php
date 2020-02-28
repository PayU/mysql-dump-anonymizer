<?php

namespace PayU\MysqlDumpAnonymizer\Tests\DataType;

use PayU\MysqlDumpAnonymizer\DataType\Json;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    private $sut;

    public function setUp(): void
    {
        parent::setUp();


        $this->sut = new Json();


    }
    public function testAnonymize()
    {

        $a = ['asd'=>'str. hello nr.1', ['q'=>'John']];
        $string = json_encode($a);
        $a = new Value('\''.addslashes($string).'\'', $string, false);

         $expected = new Value(
             '\'{"asd":"dig. nikoo oc.4","0":{"q":"Zaxs"}}\'',
             '{"asd":"str. hello nr.1","0":{"q":"John"}}"',
  false
         );

        $actual = $this->sut->anonymize($a);

        $this->assertSame($expected->getRawValue(), $actual->getRawValue());


    }
}
