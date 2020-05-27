<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizers\HashService;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\HashAnonymizer;
use PHPUnit\Framework\TestCase;

class HashAnonymizerTest extends TestCase
{

    private HashAnonymizer $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new HashAnonymizer();
    }

    /**
     * @dataProvider hashNumberProvider
     * @param string $hash
     * @param string $expected
     */
    public function testGetNextNumber(string $hash, string $expected): void
    {
        $this->sut->initializeHashString($hash);

        $actual = $this->sut->getNextNumber();
        $actual .= $this->sut->getNextNumber();
        $actual .= $this->sut->getNextNumber();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider providerIsPunctuation
     * @param $char
     * @param $expected
     */
    public function testIsPunctuation($char, $expected): void
    {
        $actual = $this->sut->isPunctuation($char);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider hashPunctuationProvider
     * @param string $hash
     * @param string $expected
     */
    public function testGetNextPunctuation(string $hash, string $expected): void
    {
        $this->sut->initializeHashString($hash);

        $actual = $this->sut->getNextPunctuation();
        $actual .= $this->sut->getNextPunctuation();
        $actual .= $this->sut->getNextPunctuation();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider hashLetterProvider
     * @param string $hash
     * @param string $expected
     */
    public function testGetNextLetter(string $hash, string $expected): void
    {
        $this->sut->initializeHashString($hash);

        $actual = $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();
        $actual .= $this->sut->getNextLetter();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider hashNumber0255Provider
     * @param string $hash
     * @param string $expected
     */
    public function testGetNextNumberBetween0And255(string $hash, array $expected): void
    {
        $this->sut->initializeHashString($hash);

        $actual[] = $this->sut->getNextNumberBetween0And255();
        $actual[] = $this->sut->getNextNumberBetween0And255();
        $actual[] = $this->sut->getNextNumberBetween0And255();

        $this->assertSame($expected, $actual);
    }

    public function hashNumberProvider(): array
    {
        return [
            [str_repeat('0', 64), '000'],
            [str_repeat('F', 64), '626'],
            ['b96a43f0c700ae55b4ac744f0b1be530976038e0abb2c2e5eba505eca99a2a47', '066'],
        ];
    }

    public function hashPunctuationProvider(): array
    {
        return [
            [str_repeat('0', 64), '`<~'],
            [str_repeat('F', 64), '>-`'],
            ['b96a43f0c700ae55b4ac744f0b1be530976038e0abb2c2e5eba505eca99a2a47', '*`='],
        ];
    }

    public function hashLetterProvider(): array
    {
        return [
            [str_repeat('0', 64), 'aaaaaaaaa'],
            [str_repeat('F', 64), 'dpqcbikgs'],
            ['b96a43f0c700ae55b4ac744f0b1be530976038e0abb2c2e5eba505eca99a2a47', 'mdkdlacks'],
            ['abcdef12312345678903afbcde983893ddee9937375032dbabeddeeaa1232343', 'aivazsgck'],
        ];
    }

    public function hashNumber0255Provider()
    {
        return [
            [str_repeat('0', 64), ['0','0','0']],
            [str_repeat('F', 64), ['255','255','255']],
            ['b96a43f0c700ae55b4ac744f0b1be530976038e0abb2c2e5eba505eca99a2a47', ['185','106','67']],
        ];
    }

    public function providerIsPunctuation(): array
    {
        return [
            ['!',true],
            ['\\',true],
            ["\n", false],
            ["\t", false],
            ["\r", false],
            [' ', false],
            ['☻', false],
        ];
    }
}
