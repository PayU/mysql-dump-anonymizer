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
                    ['a1' => 'r1a1', 'a2' => 'r1a2'],
                    ['a1' => 'r2a1', 'a2' => 'r2a2'],
                ]
            ],
            'table2' => [
                'columns' => ['b1', 'b2'],
                'values' => [
                    ['b1' => 'r1b1', 'b2' => 'r1b2'],
                    ['b1' => 'r2b1', 'b2' => 'r2b2'],
                ]
            ],
        ];


        $lineInfos = [];
        $withs = [];
        $valueMocks = [];
        $anonymizedLines = [];

        $lineNumber = 0;
        $colNumber = 0;

        foreach ($data as $table => $info) {
            $lineInfos[] = new LineInfo(
                true,
                $table,
                $info['columns'],
                $this->iterable($info['values'])
            );
            $oncePerLine = true;

            $anonymizedLines[$lineNumber] = [
                $table,
                $info['columns'],
                []
            ];

            $idx = 0;
            foreach ($info['values'] as $rowNumber => $values) {
                foreach ($values as $column => $value) {
                    $withs[] = [$table, $column];

                    if ($oncePerLine) {
                        $withs[] = [$table, $column];
                    }

                    if ($oncePerLine) {
                        $valueMocks[$colNumber] = $this->createMock(ValueAnonymizerInterface::class);
                        $valueMocks[$colNumber]->expects($this->never())->method('anonymize');
                        $colNumber++;
                    }

                    $anonymizedLines[$lineNumber][2][$idx][] = AnonymizedValue::fromUnescapedValue('anon-' . $value);

                    $valueMocks[$colNumber] = $this->createMock(ValueAnonymizerInterface::class);
                    $valueMocks[$colNumber]->expects($this->once())->method('anonymize')
                        ->with(new Value('\'' . $value . '\'', $value, false), array_map(static function ($a) {
                            return new Value('\'' . $a . '\'', $a, false);
                        }, $values))
                        ->willReturn(AnonymizedValue::fromUnescapedValue('anon-' . $value));
                    $colNumber++;
                    $oncePerLine = false;
                }
                $idx++;
            }
            $lineNumber++;
        }

        $this->lineParserMock->expects($this->exactly(2))->method('lineInfo')
            ->withConsecutive(['line1' . "\n"], ['line2'])
            ->willReturn(...$lineInfos);

        $this->anonymizationProviderMock->expects($this->exactly(2))->method('getTableAction')
            ->withConsecutive(...$this->makeConsecutiveArguments(array_keys($data)))
            ->willReturn(AnonymizationAction::ANONYMIZE, AnonymizationAction::ANONYMIZE);

        $this->anonymizationProviderMock->expects($this->exactly(10))->method('getAnonymizationFor')
            ->withConsecutive(...$withs)
            ->willReturn(...$valueMocks);

        $this->anonymizationProviderMock->expects($this->exactly(10))->method('isAnonymization')
            ->withConsecutive(...$this->makeConsecutiveArguments($valueMocks))
            ->willReturn(...array_fill(0, 10, true));

        $this->lineDumpMock->expects($this->exactly(2))->method('rebuildInsertLine')
            ->withConsecutive(...array_values($anonymizedLines))
            ->willReturn('INSERT table1' . "\n", 'INSERT table2');

        $this->observerMock->expects($this->exactly(25))->method('notify')->withConsecutive(
            [Observer::EVENT_START_READ, null],
            [Observer::EVENT_END_READ, 6],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_AFTER_LINE_PROCESSING, null],
            [Observer::EVENT_START_READ, null],
            [Observer::EVENT_END_READ, 5],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_END, $this->isType('string')],
            [Observer::EVENT_ANONYMIZATION_START, $this->isType('string')],
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
        $fp = fopen('data://text/plain;base64,' . base64_encode(implode("\n", $lines)), 'ab+');
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

    private function makeConsecutiveArguments($array)
    {
        return array_map(static function ($value) {
            return [$value];
        }, $array);
    }
}
