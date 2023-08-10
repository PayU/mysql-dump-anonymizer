<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Anonymizer;
use PayU\MysqlDumpAnonymizer\Application\Observer;
use PayU\MysqlDumpAnonymizer\Application\ObserverInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationAction;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ReadDump\LineInfo;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserInterface;
use PayU\MysqlDumpAnonymizer\WriteDump\LineDumpInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AnonymizerTest extends TestCase
{
    private Anonymizer $sut;

    /** @var AnonymizationProviderInterface|MockObject */
    private $anonymizationProviderMock;

    /** @var LineParserInterface|MockObject */
    private $lineParserMock;

    /** @var LineDumpInterface|MockObject */
    private $lineDumpMock;

    /** @var ObserverInterface|MockObject */
    private $observerMock;

    public const TMP_FILE_PATH = "/tmp/testData";

    public function setUp(): void
    {
        parent::setUp();
        $this->anonymizationProviderMock = $this->createMock(AnonymizationProviderInterface::class);
        $this->lineParserMock = $this->createMock(LineParserInterface::class);
        $this->lineDumpMock = $this->createMock(LineDumpInterface::class);
        $this->observerMock = $this->createMock(ObserverInterface::class);

        $this->sut = new Anonymizer(
            $this->anonymizationProviderMock,
            $this->lineParserMock,
            $this->lineDumpMock,
            $this->observerMock
        );
    }

    public function testRun(): void
    {
        $inputStream = $this->makeLineStream(['line1', 'line2']);
        $outputStream = $this->makeLineStream([]);

        $data = [
            'table1' => [
                'columns' => ['a1', 'a2'],
                'values' => [
                    ['r1a1', 'r1a2'],
                    ['r2a1', 'r2a2'],
                ]
            ],
            'table2' => [
                'columns' => ['b1', 'b2'],
                'values' => [
                    ['r1b1', 'r1b2'],
                    ['r2b1', 'r2b2'],
                ]
            ],
        ];

        $lineInfo1 = new LineInfo(
            true,
            'table1',
            $data['table1']['columns'],
            $this->iterable($data['table1']['values'])
        );

        $lineInfo2 = new LineInfo(
            true,
            'table2',
            $data['table2']['columns'],
            $this->iterable($data['table2']['values'])
        );


        $valueMockColumnA1 = $this->createMock(ValueAnonymizerInterface::class);
        $valueMockColumnA1->expects($this->exactly(2))->method('anonymize')
            ->withConsecutive(
                [
                    new Value('\'r1a1\'', 'r1a1', false),
                    [
                        'a1' => new Value('\'r1a1\'', 'r1a1', false),
                        'a2' => new Value('\'r1a2\'', 'r1a2', false),
                    ]
                ],
                [
                    new Value('\'r2a1\'', 'r2a1', false),
                    [
                        'a1' => new Value('\'r2a1\'', 'r2a1', false),
                        'a2' => new Value('\'r2a2\'', 'r2a2', false),
                    ]
                ]
            )
            ->willReturn(
                AnonymizedValue::fromUnescapedValue('anon-r1a1'),
                AnonymizedValue::fromUnescapedValue('anon-r2a1'),
            );

        $valueMockColumnA2 = $this->createMock(ValueAnonymizerInterface::class);
        $valueMockColumnA2->expects($this->exactly(2))->method('anonymize')
            ->withConsecutive(
                [
                    new Value('\'r1a2\'', 'r1a2', false),
                    [
                        'a1' => new Value('\'r1a1\'', 'r1a1', false),
                        'a2' => new Value('\'r1a2\'', 'r1a2', false),
                    ]
                ],
                [
                    new Value('\'r2a2\'', 'r2a2', false),
                    [
                        'a1' => new Value('\'r2a1\'', 'r2a1', false),
                        'a2' => new Value('\'r2a2\'', 'r2a2', false),
                    ]
                ]
            )
            ->willReturn(
                AnonymizedValue::fromUnescapedValue('anon-r1a2'),
                AnonymizedValue::fromUnescapedValue('anon-r2a2'),
            );

        $valueMockColumnB1 = $this->createMock(ValueAnonymizerInterface::class);
        $valueMockColumnB1->expects($this->exactly(2))->method('anonymize')
            ->withConsecutive(
                [
                    new Value('\'r1b1\'', 'r1b1', false),
                    [
                        'b1' => new Value('\'r1b1\'', 'r1b1', false),
                        'b2' => new Value('\'r1b2\'', 'r1b2', false),
                    ]
                ],
                [
                    new Value('\'r2b1\'', 'r2b1', false),
                    [
                        'b1' => new Value('\'r2b1\'', 'r2b1', false),
                        'b2' => new Value('\'r2b2\'', 'r2b2', false),
                    ]
                ]
            )
            ->willReturn(
                AnonymizedValue::fromUnescapedValue('anon-r1b1'),
                AnonymizedValue::fromUnescapedValue('anon-r2b1'),
            );

        $valueMockColumnB2 = $this->createMock(ValueAnonymizerInterface::class);
        $valueMockColumnB2->expects($this->exactly(2))->method('anonymize')
            ->withConsecutive(
                [
                    new Value('\'r1b2\'', 'r1b2', false),
                    [
                        'b1' => new Value('\'r1b1\'', 'r1b1', false),
                        'b2' => new Value('\'r1b2\'', 'r1b2', false),
                    ]
                ],
                [
                    new Value('\'r2b2\'', 'r2b2', false),
                    [
                        'b1' => new Value('\'r2b1\'', 'r2b1', false),
                        'b2' => new Value('\'r2b2\'', 'r2b2', false),
                    ]
                ]
            )
            ->willReturn(
                AnonymizedValue::fromUnescapedValue('anon-r1b2'),
                AnonymizedValue::fromUnescapedValue('anon-r2b2'),
            );


        $rebuildInsertLineWith1 = [
            'table1',
            $data['table1']['columns'],
            [
                0 => [
                    AnonymizedValue::fromUnescapedValue('anon-r1a1'),
                    AnonymizedValue::fromUnescapedValue('anon-r1a2'),
                ],
                1 => [
                    AnonymizedValue::fromUnescapedValue('anon-r2a1'),
                    AnonymizedValue::fromUnescapedValue('anon-r2a2'),
                ]
            ]
        ];

        $rebuildInsertLineWith2 = [
            'table2',
            $data['table2']['columns'],
            [
                0 => [
                    AnonymizedValue::fromUnescapedValue('anon-r1b1'),
                    AnonymizedValue::fromUnescapedValue('anon-r1b2'),
                ],
                1 => [
                    AnonymizedValue::fromUnescapedValue('anon-r2b1'),
                    AnonymizedValue::fromUnescapedValue('anon-r2b2'),
                ]
            ]
        ];

        $this->lineParserMock->expects($this->exactly(2))->method('lineInfo')
            ->withConsecutive(['line1' . "\n"], ['line2'])
            ->willReturn($lineInfo1, $lineInfo2);

        $this->anonymizationProviderMock->expects($this->exactly(2))->method('getTableAction')
            ->withConsecutive(['table1'], ['table2'])
            ->willReturn(AnonymizationAction::ANONYMIZE, AnonymizationAction::ANONYMIZE);

        $this->anonymizationProviderMock->expects($this->exactly(4))->method('getAnonymizationFor')
            ->withConsecutive(['table1', 'a1'], ['table1', 'a2'], ['table2', 'b1'], ['table2', 'b2'])
            ->willReturn($valueMockColumnA1, $valueMockColumnA2, $valueMockColumnB1, $valueMockColumnB2);

        // 1,2 , 1,2 ,  3,4,3,4
        $this->anonymizationProviderMock->expects($this->exactly(12))->method('isAnonymization')
            ->withConsecutive(
                [$valueMockColumnA1],
                [$valueMockColumnA2],
                [$valueMockColumnA1],
                [$valueMockColumnA2],
                [$valueMockColumnA1],
                [$valueMockColumnA2],
                [$valueMockColumnB1],
                [$valueMockColumnB2],
                [$valueMockColumnB1],
                [$valueMockColumnB2],
                [$valueMockColumnB1],
                [$valueMockColumnB2],
            )
            ->willReturn(...array_fill(0, 12, true));

        $this->lineDumpMock->expects($this->exactly(2))->method('rebuildInsertLine')
            ->withConsecutive($rebuildInsertLineWith1, $rebuildInsertLineWith2)
            ->willReturn('INSERT table1' . "\n", 'INSERT table2');

        $this->observerMock->expects($this->exactly(25))->method('notify')->withConsecutive(
            [Observer::EVENT_START_READ, null],
            [Observer::EVENT_END_READ, 6],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_AFTER_LINE_PROCESSING, null],
            [Observer::EVENT_START_READ, null],
            [Observer::EVENT_END_READ, 5],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('array')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_AFTER_LINE_PROCESSING, null],
            [Observer::EVENT_START_READ, null],
            [Observer::EVENT_END_READ, 0],
            [Observer::EVENT_END, null],
        );

        $this->sut->run($inputStream, $outputStream);

        $actual = [];
        rewind($outputStream);
        while ($line = fgets($outputStream)) {
            $actual[] = $line;
        }

        $this->assertSame(['INSERT table1' . "\n", 'INSERT table2'], $actual);
    }

    private function makeLineStream(array $lines)
    {

        $fp = fopen(self::TMP_FILE_PATH . (string)rand(0, 999), 'w+');
        fwrite($fp, implode("\n", $lines));

        rewind($fp);

        return $fp;
    }

    private function iterable($values)
    {
        $ret = [];
        foreach ($values as $nr => $line) {
            foreach ($line as $cell) {
                $ret[$nr][] = new Value('\'' . $cell . '\'', $cell, false);
            }
        }
        yield from $ret;
    }
}
