<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Provider;


use PayU\MysqlDumpAnonymizer\ConfigReader\YamlProviderBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class YamlProviderBuilderTest extends TestCase
{
    /**
     * @var YamlProviderBuilder|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}