<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;


final class StringHashInterfaceSha256 implements StringHashInterface
{
    private const PUNCTUATION = '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';//32

    private const NUMBERS = 'numbers';
    private const LETTERS = 'letters';
    private const SIGNS = 'signs';


    /** @var array */
    private $stacks = [
        self::NUMBERS => null,
        self::LETTERS => null,
        self::SIGNS => null,
    ];

    private $cnt = [
        self::NUMBERS => -1,
        self::LETTERS => -1,
        self::SIGNS => -1,
    ];

    /**
     * @var string
     */
    private $hash;

    private static $salt;

    public function __construct()
    {
        if (!isset(self::$salt)) {
            self::$salt = md5(microtime());
        }
    }

    public function sha256($string, $rawOutput = false): string
    {
        return hash('sha256', $string . self::$salt, $rawOutput);
    }

    public function hashMe($word, $anonymizePunctuation = false) : string
    {


        $this->cnt = [
            self::NUMBERS => -1,
            self::LETTERS => -1,
            self::SIGNS => -1,
        ];


        $word = (string)$word;

        $this->hash = $this->sha256($word.strrev($word));
        $this->hash .= $this->sha256(strrev($word).$word);

        $ret = '';

        $len = strlen($word);
        for ($i = 0; $i < $len; $i++) {
            if (ctype_digit($word[$i])) {
                $ret .= $this->getNextFromStack(self::NUMBERS);
            } elseif (ctype_upper($word[$i])) {
                $ret .= strtoupper($this->getNextFromStack(self::LETTERS));
            } elseif (ctype_lower($word[$i])) {
                $ret .= strtolower($this->getNextFromStack(self::LETTERS));
            } elseif (strpos(self::PUNCTUATION, $word[$i]) !== false) {
                if ($anonymizePunctuation) {
                    $ret .= $this->getNextFromStack(self::SIGNS);
                } else {
                    $ret .= $word[$i];
                }
            } elseif ($word[$i] === ' ') {
                $ret .= ' ';
            } elseif (in_array($word[$i], ["\n", "\r", "\t"])) {
                $ret .= $word[$i];
            } else {
                //non printable characters
                $ret .= $this->getNextFromStack(self::SIGNS);
            }
        }

        return $ret;
    }

    private function generateLetterStack(): void
    {
        $this->stacks[self::LETTERS] = str_replace(range(0, 9), '', base_convert($this->hash, 16, 36));
        if (strlen($this->stacks[self::LETTERS]) < 10) {
            //in the rare event when the sha hash has less than 10 chars
            $this->stacks[self::LETTERS] .= $this->numbersToLetters(base_convert($this->hash, 16, 10));
        }
    }

    private function generateNumbersStack(): void
    {
        $this->stacks[self::NUMBERS] = base_convert($this->hash, 16, 10); //64 length
    }

    private function generateSignsStack(): void
    {
        $choose = base_convert($this->hash, 16, 32);
        $len = strlen($choose);
        $ret = '';
        for ($i = 0; $i < $len; $i++) {
            $index = base_convert($choose[$i], 32, 10);
            $ret .= self::PUNCTUATION[$index];
        }

        $this->stacks[self::SIGNS] = $ret;
    }

    /**
     * @param string $numbers 64 number characters
     * @return string
     */
    private function numbersToLetters(string $numbers): string
    {
        $len = strlen($numbers); //len a
        $sureLetters = '';
        for ($i = 0; $i < $len; $i += 2) {
            $here = (int)"{$numbers[$i]}{$numbers[$i+1]}";
            $chr = 65;
            for ($j = 0; $j <= 99; $j += 4) {
                if ($here >= $j && $here < $j + 3) {
                    $sureLetters .= strtolower(chr($chr));
                    break;
                }
                $chr++;
            }
        }

        return $sureLetters;
    }

    private function getNextFromStack(string $string): string
    {
        if ($this->cnt[$string] === -1) {
            //first run
            if ($string === self::NUMBERS) {
                $this->generateNumbersStack();
                $this->cnt[$string] = 0;
            }
            if ($string === self::LETTERS) {
                $this->generateLetterStack();
                $this->cnt[$string] = 0;
            }
            if ($string === self::SIGNS) {
                $this->generateSignsStack();
                $this->cnt[$string] = 0;
            }
        }

        if ($this->cnt[$string] >= strlen($this->stacks[$string])) {
            $this->cnt[$string] = 0;
        }

        $ret = $this->stacks[$string][$this->cnt[$string]];
        $this->cnt[$string]++;

        return $ret;
    }
}
