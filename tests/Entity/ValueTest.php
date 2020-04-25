<?php

namespace PayU\MysqlDumpAnonymizer\Tests\Entity;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{


    public function testGetRawValue(): void
    {
        $actual = (new Value('a','b', false))->getRawValue();
        $this->assertSame('a', $actual);
    }

    public function testGetUnEscapedValue(): void
    {
        $actual = (new Value('a','b', false))->getUnEscapedValue();
        $this->assertSame('b', $actual);
    }

    public function testIsExpression(): void
    {
        $actual = (new Value('a','b', false))->isExpression();
        $this->assertFalse($actual);
    }
}
