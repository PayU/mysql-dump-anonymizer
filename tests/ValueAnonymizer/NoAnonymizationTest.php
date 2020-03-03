<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\NoAnonymization;
use PHPUnit\Framework\TestCase;

class NoAnonymizationTest extends TestCase
{
    /**
     * @var NoAnonymization
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new NoAnonymization();
    }

    public function testNoAnonymization(): void
    {
        $actual = $this->sut->anonymize(
            new Value('\'safe value\'', 'safe value', false),
            [],
            new Config()
        );

        $this->assertSame('\'safe value\'', $actual->getRawValue());
    }
}
