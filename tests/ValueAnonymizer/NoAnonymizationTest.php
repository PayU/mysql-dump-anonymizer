<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ReadDump\Value;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\NoAnonymization;
use PHPUnit\Framework\TestCase;

class NoAnonymizationTest extends TestCase
{

    public function testNoAnonymization(): void
    {
        $actual = (new NoAnonymization())->anonymize(
            new Value('\'safe value\'', 'safe value', false), []
        );

        $this->assertSame('\'safe value\'', $actual->getRawValue());
    }
}
