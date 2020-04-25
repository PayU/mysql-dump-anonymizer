<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ConfigReader;


use PayU\MysqlDumpAnonymizer\ConfigReader\ProviderFactory;
use PayU\MysqlDumpAnonymizer\ConfigReader\YamlProviderBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ProviderFactoryTest extends TestCase
{
    /**
     * @var ProviderFactory|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new ProviderFactory();
    }

    public function testMakeOk(): void
    {
        $actual = $this->sut->make(ProviderFactory::DEFAULT_CONFIG_TYPE, '/path/file');
        $this->assertInstanceOf(YamlProviderBuilder::class, $actual);
    }

    public function testMakeFail(): void
    {
        $this->expectException(RuntimeException::class);
        $this->sut->make('NOT-FOUND-CONFIG-TYPE', '/path/file');
    }

}