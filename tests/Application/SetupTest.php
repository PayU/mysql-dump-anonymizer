<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Application;

use Exception;
use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProvider;
use PayU\MysqlDumpAnonymizer\Application\CommandLineParametersInterface;
use PayU\MysqlDumpAnonymizer\Application\Observer\ProcessObserverInterface;
use PayU\MysqlDumpAnonymizer\Application\ObserverInterface;
use PayU\MysqlDumpAnonymizer\Application\Setup;
use PayU\MysqlDumpAnonymizer\ReadDump\MySqlDumpLineParser;
use PayU\MysqlDumpAnonymizer\WriteDump\MysqlLineDump;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SetupTest extends TestCase
{
    private Setup $sut;

    /** @var CommandLineParametersInterface|MockObject */
    private $commandLineParametersMock;

    /** @var ObserverInterface|MockObject */
    private $observerMock;

    public function setUp(): void
    {
        $this->commandLineParametersMock = $this->createMock(CommandLineParametersInterface::class);
        $this->observerMock = $this->createMock(ObserverInterface::class);
        $this->sut = new Setup($this->commandLineParametersMock, $this->observerMock);
        parent::setUp();
    }

    public function testSetupOk(): void
    {
        $this->commandLineParametersMock
            ->expects($this->once())
            ->method('setCommandLineArguments');
        $this->commandLineParametersMock
            ->expects($this->once())
            ->method('validate');
        $this->commandLineParametersMock
            ->expects($this->once())
            ->method('isShowProgress')
            ->willReturn(true);
        $this->observerMock
            ->expects($this->once())
            ->method('registerObserver')
            ->with($this->isInstanceOf(ProcessObserverInterface::class));

        try {
            $this->sut->setup();
        } catch (Exception $e) {
            $this->assertTrue(false, 'No expected exception: ' . get_class($e) . ' ' . $e->getMessage());
        }
    }

    public function testSetupFail(): void
    {
        $this->commandLineParametersMock
            ->expects($this->once())
            ->method('setCommandLineArguments');

        $this->commandLineParametersMock
            ->expects($this->once())
            ->method('validate')
            ->willThrowException(new InvalidArgumentException());

        $this->commandLineParametersMock
            ->expects($this->never())
            ->method('isShowProgress');
        $this->observerMock
            ->expects($this->never())
            ->method('registerObserver');

        $this->expectException(InvalidArgumentException::class);

        $this->sut->setup();
    }

    public function testGetLineParser(): void
    {
        $this->commandLineParametersMock->expects($this->once())
            ->method('getLineParser')
            ->willReturn('mysqldump');

        $actual = $this->sut->getLineParser();

        $this->assertInstanceOf(MySqlDumpLineParser::class, $actual);
    }

    public function testGetAnonymizationProvider(): void
    {
        $this->commandLineParametersMock->method('getConfigType')
            ->willReturn('yaml');
        $this->commandLineParametersMock->method('getConfigFile')
            ->willReturn(dirname(__DIR__, 2) .DIRECTORY_SEPARATOR.'sample'.DIRECTORY_SEPARATOR.'anon.yml');

        $actual = $this->sut->getAnonymizationProvider();
        $this->assertInstanceOf(AnonymizationProvider::class, $actual);
    }

    public function testGetLineDump(): void
    {
        $actual = $this->sut->getLineDump();
        $this->assertInstanceOf(MysqlLineDump::class, $actual);
    }
}
