<?php

namespace PayU\MysqlDumpAnonymizer\Tests\Application\Observer;

use PayU\MysqlDumpAnonymizer\Application\Observer\Progress;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class ProgressTest extends TestCase
{
    private Progress $sut;

    public function setUp(): void
    {
        $this->sut = new Progress();
        parent::setUp();
    }

    public function testOnBegin(): void
    {
        $this->sut->onBegin(123);
        $this->assertSame(123, $this->getProperty('total'));
        $this->assertIsFloat($this->getProperty('startedAt'));
    }


    public function testOnStartReadLine(): void
    {
        $this->sut->onStartReadLine();
        $this->assertIsFloat($this->getProperty('startReadLine'));
    }

    public function testOnFinishReadLine(): void
    {
        $this->sut->onFinishReadLine(10);
        $this->sut->onFinishReadLine(15);

        $this->assertSame(25, $this->getProperty('totalReadBytes'));
    }

    public function testOnNullValue(): void
    {
        $this->sut->onNullValue('namespace\\FreeText');
        $this->sut->onNullValue('namespace\\FreeText');
        $this->sut->onNullValue('namespace\\SomeType');
        $this->assertSame(2, $this->getProperty('anonymizationTypes')['FreeText']['nulls']);
        $this->assertSame(1, $this->getProperty('anonymizationTypes')['SomeType']['nulls']);
    }

    public function testOnAnonymizationStart(): void
    {
        $this->sut->onAnonymizationFinish('namespace\\FreeText');
        $this->sut->onAnonymizationFinish('namespace\\FreeText');
        $this->sut->onAnonymizationFinish('namespace\\SomeType');
        $this->assertIsFloat($this->getProperty('anonymizationTypes')['FreeText']['time']);
        $this->assertIsFloat($this->getProperty('anonymizationTypes')['SomeType']['time']);
        $this->assertIsFloat($this->getProperty('anonymizationTotal'));
    }

    public function testOnAnonymizationFinish(): void
    {
        $this->sut->onAnonymizationStart('namespace\\FreeText', 1);
        $this->sut->onAnonymizationStart('namespace\\FreeText', 1);
        $this->sut->onAnonymizationStart('namespace\\SomeType', 1);
        $this->assertSame(2, $this->getProperty('anonymizationTypes')['FreeText']['count']);
        $this->assertSame(1, $this->getProperty('anonymizationTypes')['SomeType']['count']);

        $this->assertIsFloat($this->getProperty('anonymizationTypeStart'));
    }

    public function testOnNotAnInsertLine(): void
    {
        $this->sut->onNotAnInsertLine();
        $this->sut->onNotAnInsertLine();
        $this->sut->onNotAnInsertLine();
        $this->assertSame(3, $this->getProperty('notAnInsert'));
    }

    public function testOnTruncateLine(): void
    {
        $this->sut->onTruncateLine();
        $this->sut->onTruncateLine();
        $this->sut->onTruncateLine();
        $this->assertSame(3, $this->getProperty('truncate'));
    }

    public function testOnNoNeedForAnonymizationInsertLine(): void
    {
        $this->sut->onNoNeedForAnonymizationInsertLine();
        $this->sut->onNoNeedForAnonymizationInsertLine();
        $this->sut->onNoNeedForAnonymizationInsertLine();
        $this->assertSame(3, $this->getProperty('insertLineNoNeedForAnonymization'));
    }


    public function testOnAfterEachLineProcessing(): void
    {
        $this->setOutputStream();

        $this->sut->onBegin(10000);
        $this->sut->onStartReadLine();
        $this->sut->onAnonymizationStart('namespace\\Something', 1);
        $this->sut->onAnonymizationFinish('namespace\\Something');
        $this->sut->onFinishReadLine(10);

        $this->sut->onStartReadLine();
        $this->sut->onAnonymizationStart('namespace\\SomethingElse', 1);
        $this->sut->onAnonymizationFinish('namespace\\SomethingElse');
        $this->sut->onFinishReadLine(15);


        $this->sut->onAfterEachLineProcessing();

        $fp = $this->getOutputStream();
        rewind($fp);

        $content = '';
        while ($line = fgets($fp)) {
            $content .= $line;
        }

        $this->assertStringContainsString('25/10000 bytes', $content);
        $this->assertStringContainsString('Used Memory', $content);
        $this->assertStringContainsString('Running time', $content);
        $this->assertStringContainsString('Read time', $content);
        $this->assertStringContainsString('Anonymization', $content);
        $this->assertStringContainsString('Something:', $content);
        $this->assertStringContainsString('SomethingElse:', $content);

        fclose($fp);
    }

    public function testOnEnd(): void
    {
        $this->setOutputStream();

        $this->sut->onEnd();

        $fp = $this->getOutputStream();
        rewind($fp);

        $this->assertSame(PHP_EOL, fgets($fp));

        fclose($fp);
    }


    private function getProperty($name)
    {
        $refObject = new ReflectionObject($this->sut);
        $refProperty = $refObject->getProperty($name);
        $refProperty->setAccessible(true);
        return $refProperty->getValue($this->sut);
    }

    private function setOutputStream()
    {
        $fp = fopen('data://text/plain;base64,', 'ab+');
        rewind($fp);

        $refObject = new ReflectionObject($this->sut);
        $refProperty = $refObject->getProperty('output');
        $refProperty->setAccessible(true);
        $refProperty->setValue($this->sut, $fp);
    }

    private function getOutputStream()
    {
        $refObject = new ReflectionObject($this->sut);
        $refProperty = $refObject->getProperty('output');
        $refProperty->setAccessible(true);
        return $refProperty->getValue($this->sut);
    }
}
