<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers\HashService;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\HashAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashSha256;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class StringHashSha256Test extends TestCase
{
    /**
     * @var StringHashSha256|MockObject
     */
    private $sut;

    /** @var HashAnonymizerInterface|MockObject */
    private $hashAnonymizerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->hashAnonymizerMock = $this->createMock(HashAnonymizerInterface::class);

        $obj = new StringHashSha256($this->hashAnonymizerMock);
        $refObject = new ReflectionObject($obj);
        $refProperty = $refObject->getProperty('salt');
        $refProperty->setAccessible(true);
        $refProperty->setValue($obj, 'test-salt');

        $this->sut = $obj;
    }

    public function testWithMultiByteCharacter(): void
    {
        $multiByteChar = '☻';

        $input = 'Str. Hello 1213 AA-AA|A&AA%' . $multiByteChar . 'qwert ';
        $expected = 'Oss. Cwogo 0288 KW-WO|C&CS%!asdfg ';

        $this->hashAnonymizerMock->expects($this->once())->method('initializeHashString')
            ->with('014c4dfea5ea863582213117f232fbb975e6c39f9fef97c65995e8878111b17419efe951085d8f1b8229a6c6'.
                '271335927118a3a2d26c08469d2758cc90a06184');

        $this->hashAnonymizerMock->expects($this->exactly(4))
            ->method('getNextNumber')
            ->willReturn('0', '2', '8', '8');

        $this->hashAnonymizerMock->expects($this->exactly(20))
            ->method('getNextLetter')
            ->willReturn(...str_split('OssCwogoKWWOCCSasdfg'));

        $this->hashAnonymizerMock->expects($this->exactly(6))
            ->method('isPunctuation')
            ->withConsecutive(...array_map(static function ($v) {
                return [$v];
            }, mb_str_split('.-|&%' . $multiByteChar . '')))
            ->willReturn(true, true, true, true, true, false);

        $this->hashAnonymizerMock->expects($this->once())->method('getNextPunctuation')->willReturn('!');

        $actual = $this->sut->hashKeepFormat($input);
        $this->assertSame($expected, $actual);

    }

    public function testWithoutMultiByteCharacter(): void
    {

        $input = 'Str. Hello ' . "\n" . '1213 AA-AA|A&AA%qwert ';
        $expected = 'Oss$ Cwogo ' . "\n" . '0288 KW^WO+C!CS*asdfg ';

        $this->hashAnonymizerMock->expects($this->once())->method('initializeHashString')
        ->with('087b46d2a406e8c0763370b101e1df23f9edfe58207cf609fd7b1753a70adc6d2aaf2ddbee53567fb20085fb79a73b82'.
            'a6836e74e252fa6b132cf17563eced9d');

        $this->hashAnonymizerMock->expects($this->exactly(4))
            ->method('getNextNumber')
            ->willReturn('0', '2', '8', '8');

        $this->hashAnonymizerMock->expects($this->exactly(20))
            ->method('getNextLetter')
            ->willReturn(...str_split('OssCwogoKWWOCCSasdfg'));

        $this->hashAnonymizerMock->expects($this->exactly(5))
            ->method('isPunctuation')
            ->withConsecutive(['.'], ['-'], ['|'], ['&'], ['%'])
            ->willReturn(true, true, true, true, true);

        $this->hashAnonymizerMock->expects($this->exactly(5))
            ->method('getNextPunctuation')->willReturn('$','^','+','!','*');

        $actual = $this->sut->hashKeepFormat($input, true);
        $this->assertSame($expected, $actual);

    }

    public function testHashIpAddressString()
    {
        $input = '1.2.3.4';
        $this->hashAnonymizerMock->expects($this->once())->method('initializeHashString')
            ->with('0ec8fe5d3a508da7b07751332c420f895739e5b3311cd6c80a349287ad0b719e');

        $this->hashAnonymizerMock->expects($this->exactly(4))->method('getNextNumberBetween0And255')
            ->willReturn('22','44','55','66');

        $actual = $this->sut->hashIpAddressString($input);

        $this->assertSame('22.44.55.66', $actual);

    }

}
