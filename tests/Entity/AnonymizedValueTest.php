<?php

namespace PayU\MysqlDumpAnonymizer\Tests\Entity;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PHPUnit\Framework\TestCase;

class AnonymizedValueTest extends TestCase
{

    public function testGetRawValue(): void
    {
        $actual = AnonymizedValue::fromRawValue('NULL');
        $this->assertSame('NULL', $actual->getRawValue());
    }

    public function testFromUnescapedValue(): void
    {
        $actual = AnonymizedValue::fromUnescapedValue('test\'tes\\'."\n".'te');
        $this->assertSame('\'test\\\'tes\\\\\\nte\'', $actual->getRawValue());
    }

    public function testFromOriginalValue(): void
    {
        $actual = AnonymizedValue::fromOriginalValue(new Value('NULL','NULL', true));
        $this->assertSame('NULL', $actual->getRawValue());

    }

    public function testFromRawValue(): void
    {
        $actual = AnonymizedValue::fromRawValue('\'NULL\'');
        $this->assertSame('\'NULL\'', $actual->getRawValue());
    }
}
