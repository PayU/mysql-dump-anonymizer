<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ConfigReader;


use Exception;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProvider;
use PayU\MysqlDumpAnonymizer\ConfigReader\ValueAnonymizerFactoryInterface;
use PayU\MysqlDumpAnonymizer\ConfigReader\YamlProviderBuilder;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationAction;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Eav;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Email;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\HashAnonymizer;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\NoAnonymization;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Phone;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashSha256;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

final class YamlProviderBuilderTest extends TestCase
{
    private const YML_FILE = 'path/file.txt';

    /** @var YamlProviderBuilder|MockObject */
    private $sut;

    /** @var MockObject|ValueAnonymizerFactoryInterface */
    private $valueAnonymizerMock;

    /** @var MockObject|Parser */
    private $parserMock;

    public function setUp(): void
    {
        $this->parserMock = $this->createMock(Parser::class);
        $this->valueAnonymizerMock = $this->createMock(ValueAnonymizerFactoryInterface::class);

        $this->sut = new YamlProviderBuilder(
            self::YML_FILE,
            $this->parserMock,
            $this->valueAnonymizerMock
        );

        parent::setUp();
    }

    public function testValidate()
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'col1', 'DataType' => 'type1'],
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol=EMAIL'],
                        ['ColumnName' => 'eav', 'DataType' => 'type3', 'Where' => 'somecol=PHONE']
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->exactly(3))
            ->method('valueAnonymizerExists')
            ->withConsecutive(['type1'], ['type2'], ['type3'])
            ->willReturn(true, true, true);

        try {
            $this->sut->validate();
        } catch (Exception $e) {
            $this->assertTrue(false, 'Unexpected exception : ' . get_class($e) . ' : ' . $e->getMessage());
        }

    }


    public function testValidateFailParseError(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willThrowException(new ParseException('test'));

        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');

        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Cannot parse yml format(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailNoArray(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn(['tableName' => 'bad']);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - second level must be array(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadActionKey(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn(['tableName' => ['BadAction' => 'anonymize']]);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - Action key must be present(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadActionValue(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn(['tableName' => ['Action' => 'bad-action-name']]);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid Action -(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadColumnsKey(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'FAIL-Columns' => '',
                ]
            ]);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - Columns key must be present(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadColumnsValues(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [''],
                ]
            ]);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - column data not array -(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadColumnsColKey(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['BadColumnName' => 'colName']
                    ],
                ]
            ]);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - no column name key -(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadColumnsNoDataKey(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'colName']
                    ],
                ]
            ]);
        $this->valueAnonymizerMock->expects($this->never())->method('valueAnonymizerExists');
        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - no data type key -(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailBadColumnsNoAnonDataType(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'colName', 'DataType' => 'type1']
                    ],
                ]
            ]);
        $this->valueAnonymizerMock->expects($this->once())->method('valueAnonymizerExists')->with('type1')
            ->willReturn(false);

        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - invalid data type key -(.*)$#i');

        $this->sut->validate();
    }


    public function testValidateFailMixedEavNormal1(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'eav', 'DataType' => 'type1'],
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol=EMAIL'],
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->exactly(2))
            ->method('valueAnonymizerExists')
            ->withConsecutive(['type1'], ['type2'])
            ->willReturn(true, true);

        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - mixed eav/normal data type 1(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailMixedEavNormal2(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol=EMAIL'],
                        ['ColumnName' => 'eav', 'DataType' => 'type1'],
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->exactly(2))
            ->method('valueAnonymizerExists')
            ->withConsecutive(['type2'], ['type1'])
            ->willReturn(true, true);

        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - mixed eav/normal data type 2(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailMixedEavBadWhere(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol fara-egal EMAIL'],
                        ['ColumnName' => 'test', 'DataType' => 'type1'],
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->once())
            ->method('valueAnonymizerExists')
            ->withConsecutive(['type2'])
            ->willReturn(true);

        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - invalid where -(.*)$#i');

        $this->sut->validate();
    }

    public function testValidateFailMixedEavMultiAttr(): void
    {
        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol1=EMAIL'],
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol2=PHONE'],
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->exactly(2))
            ->method('valueAnonymizerExists')
            ->withConsecutive(['type2'], ['type2'])
            ->willReturn(true, true);

        $this->expectException(ConfigValidationException::class);
        $this->expectExceptionMessageMatches('#^Invalid config - EAV Column multiple attributes(.*)$#i');

        $this->sut->validate();
    }

    public function testBuildProviderOkEav(): void
    {
        $emailObj = new Email(new StringHashSha256(new HashAnonymizer()));
        $phoneObj = new Phone(new StringHashSha256(new HashAnonymizer()));

        $expected = new AnonymizationProvider(
            ['tableName' => AnonymizationAction::ANONYMIZE],
            AnonymizationAction::ANONYMIZE,
            [
                'tableName' => [
                    'eav' => new Eav('somecol', [
                        'EMAIL' => $emailObj,
                        'PHONE' => $phoneObj,
                    ])
                ]
            ],
            new NoAnonymization()
        );

        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol=EMAIL'],
                        ['ColumnName' => 'eav', 'DataType' => 'type2', 'Where' => 'somecol=PHONE'],
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->exactly(3))->method('getValueAnonymizerClass')
            ->withConsecutive(
                ['type2', []],
                ['type2', []],
                ['Eav',
                    [
                        'somecol',
                        [
                            'EMAIL' => $emailObj,
                            'PHONE' => $phoneObj,
                        ]
                    ]
                ]
            )->willReturn(
                $emailObj,
                $phoneObj,
                new Eav('somecol', [
                    'EMAIL' => $emailObj,
                    'PHONE' => $phoneObj,
                ]));

        $actual = $this->sut->buildProvider();
        $this->assertEquals($expected, $actual);
    }

    public function testBuildProviderOkNormal(): void
    {
        $emailObj = new Email(new StringHashSha256(new HashAnonymizer()));
        $phoneObj = new Phone(new StringHashSha256(new HashAnonymizer()));

        $expected = new AnonymizationProvider(
            ['truncateMe' => AnonymizationAction::TRUNCATE, 'tableName' => AnonymizationAction::ANONYMIZE],
            AnonymizationAction::ANONYMIZE,
            [
                'truncateMe' => [],
                'tableName' => [
                    'emailField' => $emailObj,
                    'phoneField' => $phoneObj,
                ]
            ],
            new NoAnonymization()
        );

        $this->parserMock->expects($this->once())->method('parseFile')->with(self::YML_FILE)
            ->willReturn([
                'truncateMe' => [
                    'Action' => 'truncate'
                ],
                'tableName' => [
                    'Action' => 'anonymize',
                    'Columns' => [
                        ['ColumnName' => 'emailField', 'DataType' => 'type1'],
                        ['ColumnName' => 'phoneField', 'DataType' => 'type2'],
                    ]
                ]
            ]);

        $this->valueAnonymizerMock->expects($this->exactly(2))->method('getValueAnonymizerClass')
            ->withConsecutive(
                ['type1', []],
                ['type2', []],
            )->willReturn(
                $emailObj,
                $phoneObj,
            );

        $actual = $this->sut->buildProvider();
        $this->assertEquals($expected, $actual);
    }


}