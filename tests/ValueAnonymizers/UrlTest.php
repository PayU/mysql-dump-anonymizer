<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Url;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{

    /** @var StringHashInterface|MockObject */
    private $stringHashMock;

    private Url $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->stringHashMock = $this->createMock(StringHashInterface::class);
        $this->sut = new Url($this->stringHashMock);
    }


    public function testAnonymizeUrlWithScheme(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->willReturn('ubp.huhkosocgww.og/wwgwoockkokcg');

        $actual = $this->sut->anonymize(
            new Value('\'http://www.some.hu/path\'', 'http://www.some.hu/path', false),
            []
        );

        $this->assertSame('\'http://ubp.huhkosocgww.og/wwgwoockkokcg\'', $actual->getRawValue());
    }

    public function testAnonymizeUrlWithoutScheme(): void
    {
        $this->stringHashMock->expects($this->once())->method('hashKeepFormat')->with('www.alphabank.ro')
            ->willReturn('ubp.huhkosocg.og');

        $actual = $this->sut->anonymize(
            new Value('\'www.alphabank.ro\'', 'www.alphabank.ro', false),
            []
        );

        $this->assertSame('\'ubp.huhkosocg.og\'', $actual->getRawValue());
    }

    public function testAnonymizeUrlExpr(): void
    {
        $this->stringHashMock->expects($this->never())->method('hashKeepFormat');

        $actual = $this->sut->anonymize(
            new Value('NULL', 'NULL', true),
            []
        );

        $this->assertSame('NULL', $actual->getRawValue());
    }
}
