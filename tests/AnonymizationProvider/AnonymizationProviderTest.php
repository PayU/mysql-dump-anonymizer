<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\AnonymizationProvider;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProvider;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationAction;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\NoAnonymization;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AnonymizationProviderTest extends TestCase
{
    private AnonymizationProvider $sut;

    /** @var FreeText | MockObject  */
    private MockObject $freeTextMock;

    /** @var NoAnonymization | MockObject  */
    private $noAnonymizationMock;

    public function setUp(): void
    {
        $this->freeTextMock = $this->getMockBuilder(ValueAnonymizerInterface::class)
            ->setMockClassName('FreeText')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $this->noAnonymizationMock = $this->getMockBuilder(ValueAnonymizerInterface::class)
            ->setMockClassName(AnonymizationProvider::NO_ANONYMIZATION)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $this->sut = new AnonymizationProvider(
            [
                'tableName' => AnonymizationAction::ANONYMIZE
            ],
            AnonymizationAction::TRUNCATE,
            [
                'tableName' => [
                    'col1' => $this->freeTextMock
                ]
            ],
            $this->noAnonymizationMock
        );

    }

    public function testGetTableActionExists(): void
    {
        $expected = AnonymizationAction::ANONYMIZE;
        $actual = $this->sut->getTableAction('tableName');
        $this->assertSame($expected, $actual);
    }

    public function testGetTableActionDoesNotExists(): void
    {
        $expected = AnonymizationAction::TRUNCATE;
        $actual = $this->sut->getTableAction('NOT_EXIST_!!');
        $this->assertSame($expected, $actual);
    }

    public function testGetAnonymizationForExists(): void
    {
        $actual = $this->sut->getAnonymizationFor('tableName', 'col1');
        $this->assertSame($this->freeTextMock, $actual);
        $this->assertNotSame($this->noAnonymizationMock, $actual);
    }

    public function testGetAnonymizationForDoesNotExists(): void
    {
        $actual = $this->sut->getAnonymizationFor('tableName', 'DOESNT-EXIST');
        $this->assertNotSame($this->freeTextMock, $actual);
        $this->assertSame($this->noAnonymizationMock, $actual);
    }


    public function testIsAnonymizationTrue(): void
    {
        $actual = $this->sut->isAnonymization($this->freeTextMock);
        $this->assertTrue($actual);
    }

    public function testIsAnonymizationFalse(): void
    {
        $actual = $this->sut->isAnonymization($this->noAnonymizationMock);
        $this->assertFalse($actual);
    }
}