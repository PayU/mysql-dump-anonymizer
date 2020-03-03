<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\LineParser\MySqlDumpLineParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MySqlDumpLineParserTest extends TestCase
{


    /**
     * @var MySqlDumpLineParser|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new MySqlDumpLineParser();
    }

    public function testActionIndexSuccess(): void
    {
        $query = <<<'EOD'
INSERT INTO `example_1` (`id`, `fname`, `json`, `dated`, `comment`) VALUES
 (1,'Alex','{}','2020-02-20 02:20:02','Lorem ipsum dolor sit amet'),
(2,'Gigi','{\"key1\":\"test data\",\"key2\":{\"0\":1,\"1\":2,\"2\":3,\"key2-1\":\"yellow\"},\"0\":1,\"1\":2,\"2\":3}','1998-02-02 02:02:02','My email is asd@asd.com'),
(3,'Bjorg',NULL,'1980-10-10 10:10:10','Hello world'),
(4,'Artemis','[1,2,3]','2020-01-01 01:01:01','pur şi simplu o machetă încă din secolul al XVI-lea, când un tipograf anonim popularizată în anii \'60 odată cu'),
(5,'QuoteMe',NULL,'2020-02-25 12:05:08','I want 2 backslash here [\\\\] and backslash quote here [\\\"]'),
(6,'NewLine',NULL,'2020-01-01 01:11:12','After this there is a backslash-new line-text-newline-backslash [\\\r\nhello\r\n\\]  double-single [\"\']');
EOD;
        $query = str_replace(["\r", "\n"], '', $query);

        $expectedRawNowDoc1 = <<<'EOD'
'I want 2 backslash here [\\\\] and backslash quote here [\\\"]'
EOD;
        $expectedEscapedNowDoc1 = <<<'EOD'
I want 2 backslash here [\\] and backslash quote here [\"]
EOD;

        $expectedRawNowDoc2 = <<<'EOD'
'After this there is a backslash-new line-text-newline-backslash [\\\r\nhello\r\n\\]  double-single [\"\']'
EOD;
        $expectedEscapedNowDoc2_line1 = <<<'EOD'
After this there is a backslash-new line-text-newline-backslash [\
EOD;
        $expectedEscapedNowDoc2_line2 = <<<'EOD'
hello
EOD;
        $expectedEscapedNowDoc2_line3 = <<<'EOD'
\]  double-single ["']
EOD;
        $expectedEscapedNowDoc2 = $expectedEscapedNowDoc2_line1."\r\n".$expectedEscapedNowDoc2_line2."\r\n".$expectedEscapedNowDoc2_line3;


        $expected = [
            [
                ['1', '1', true],
                ['\'Alex\'', 'Alex', false],
                ['\'{}\'', '{}', false],
                ['\'2020-02-20 02:20:02\'', '2020-02-20 02:20:02', false],
                ['\'Lorem ipsum dolor sit amet\'', 'Lorem ipsum dolor sit amet', false]
            ], [
                ['2', '2', true],
                ['\'Gigi\'', 'Gigi', false],
                ['\'{\"key1\":\"test data\",\"key2\":{\"0\":1,\"1\":2,\"2\":3,\"key2-1\":\"yellow\"},\"0\":1,\"1\":2,\"2\":3}\'',
                    '{"key1":"test data","key2":{"0":1,"1":2,"2":3,"key2-1":"yellow"},"0":1,"1":2,"2":3}', false],
                ['\'1998-02-02 02:02:02\'', '1998-02-02 02:02:02', false],
                ['\'My email is asd@asd.com\'', 'My email is asd@asd.com', false]
            ], [
                ['3', '3', true],
                ['\'Bjorg\'', 'Bjorg', false],
                ['NULL', 'NULL', true],
                ['\'1980-10-10 10:10:10\'', '1980-10-10 10:10:10', false],
                ['\'Hello world\'', 'Hello world', false]
            ], [
                ['4', '4', true],
                ['\'Artemis\'', 'Artemis', false],
                ['\'[1,2,3]\'', '[1,2,3]', false],
                ['\'2020-01-01 01:01:01\'', '2020-01-01 01:01:01', false],
                ['\'pur şi simplu o machetă încă din secolul al XVI-lea, când un tipograf anonim popularizată în anii \\\'60 odată cu\'',
                    'pur şi simplu o machetă încă din secolul al XVI-lea, când un tipograf anonim popularizată în anii \'60 odată cu', false]
            ], [
                ['5', '5', true],
                ['\'QuoteMe\'', 'QuoteMe', false],
                ['NULL', 'NULL', true],
                ['\'2020-02-25 12:05:08\'', '2020-02-25 12:05:08', false],
                [$expectedRawNowDoc1, $expectedEscapedNowDoc1, false]
            ], [
                ['6', '6', true],
                ['\'NewLine\'', 'NewLine', false],
                ['NULL', 'NULL', true],
                ['\'2020-01-01 01:11:12\'', '2020-01-01 01:11:12', false],
                [$expectedRawNowDoc2, $expectedEscapedNowDoc2, false]
            ],
        ];


        $cnt1 = 0;
        /** @var LineInfo $lineInfo */
        $lineInfo = $this->sut->lineInfo($query);

        foreach ($lineInfo->getValuesParser() as $row) {
            /** @var Value[] $row */
            $cnt2 = 0;
            $this->assertIsArray($row, 'Invalid row at '.$cnt1.'-'.$cnt2);
            foreach ($row as $value) {
                $this->assertInstanceOf(Value::class, $value, 'Not a value object at '.$cnt1.'-'.$cnt2);
                $this->assertSame($expected[$cnt1][$cnt2][0], $value->getRawValue(), 'Invalid raw value at '.$cnt1.'-'.$cnt2);
                $this->assertSame($expected[$cnt1][$cnt2][1], $value->getUnEscapedValue(), 'Invalid un-escaped value at '.$cnt1.'-'.$cnt2);
                $this->assertSame($expected[$cnt1][$cnt2][2], $value->isExpression(), 'Invalid expression setting at '.$cnt1.'-'.$cnt2);
                $cnt2++;
            }
            $cnt1++;
        }
        //var_dump($actual);
    }
}
