<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ReadDump;

use \RuntimeException;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserFactory;
use PayU\MysqlDumpAnonymizer\ReadDump\MySqlDumpLineParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LineParserFactoryTest extends TestCase
{
    /**
     * @var LineParserFactory|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new LineParserFactory();
    }

    public function testChooseLineParser()
    {
        $actual = $this->sut->chooseLineParser(LineParserFactory::LINE_PARSER_MYSQL_DUMP);
        $this->assertInstanceOf(MySqlDumpLineParser::class, $actual);
    }

    public function testChooseLineParserException()
    {
        $this->expectException(RuntimeException::class);
        $this->sut->chooseLineParser('doesntExist');
    }
}
