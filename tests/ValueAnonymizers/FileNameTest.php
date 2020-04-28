<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FileName;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileNameTest extends TestCase
{
    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private FileName $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new FileName($this->stringHashMock);
    }

    public function testAnonymize(): void
    {

        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('/odfeb/ymt_wecgccw');

        $input = '/admin/log_monitor.php';
        $val = new Value('\''.$input.'\'', $input, false);
        $actual = $this->sut->anonymize($val, []);

        $this->assertSame('\'/odfeb/ymt_wecgccw.php\'', $actual->getRawValue());
    }

    public function testAnonymizeReturnSameValueIfUnquoted(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashKeepFormat');
        $actual = $this->sut->anonymize(new Value('NULL', 'NULL', true), []);
        $this->assertSame('NULL', $actual->getRawValue());
    }
}
