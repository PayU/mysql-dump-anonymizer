<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Application;

use Exception;
use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\Application\CommandLineParameters;
use PayU\MysqlDumpAnonymizer\ConfigReader\ProviderFactory;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserFactory;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class CommandLineParametersTest extends TestCase
{
    /**
     * @var CommandLineParameters
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new CommandLineParameters();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidateOk(): void
    {
        $this->setPrivateArguments(['config' => 'dir/file.ext']);

        try {
            $this->sut->validate();
        } catch (Exception $e) {
            $this->assertTrue(false, 'Unexpected exception ' . get_class($e) . ' : ' . $e->getMessage());
        }
    }

    public function testValidateFail(): void
    {
        $this->setPrivateArguments(['config' => '']);
        $this->expectException(InvalidArgumentException::class);
        $this->sut->validate();
    }

    public function testHelp(): void
    {
        $actual = $this->sut->help();
        $this->assertStringContainsString('Usage:', $actual);
    }

    public function testGetConfigTypeDefault(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext'
        ]);

        $actual = $this->sut->getConfigType();
        $this->assertSame(ProviderFactory::DEFAULT_CONFIG_TYPE, $actual);
    }

    public function testGetConfigTypeOther(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
            'config-type' => 'other'
        ]);

        $actual = $this->sut->getConfigType();
        $this->assertSame('other', $actual);
    }

    public function testGetConfigFile(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
        ]);

        $actual = $this->sut->getConfigFile();
        $this->assertSame('dir/file.ext', $actual);
    }

    public function testGetLineParserDefault(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
        ]);

        $actual = $this->sut->getLineParser();
        $this->assertSame(LineParserFactory::LINE_PARSER_MYSQL_DUMP, $actual);
    }

    public function testGetLineParserOther(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
            'line-parser' => 'heidi'
        ]);

        $actual = $this->sut->getLineParser();
        $this->assertSame('heidi', $actual);
    }


    public function testGetEstimated(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
            'dump-size' => '123'
        ]);

        $actual = $this->sut->getEstimatedDumpSize();
        $this->assertSame(123, $actual);
    }

    public function testGetEstimatedDefault(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
        ]);

        $actual = $this->sut->getEstimatedDumpSize();
        $this->assertSame(1000000000, $actual);
    }

    public function testIsShowProgress(): void
    {
        $this->setPrivateArguments([
            'config' => 'dir/file.ext',
            'show-progress' => '0'
        ]);

        $actual = $this->sut->isShowProgress();
        $this->assertFalse($actual);
    }

    private function setPrivateArguments($arguments): void
    {
        $refObject = new ReflectionObject($this->sut);
        $refProperty = $refObject->getProperty('arguments');
        $refProperty->setAccessible(true);
        $refProperty->setValue($this->sut, $arguments);

        $this->sut->setCommandLineArguments();
    }

}