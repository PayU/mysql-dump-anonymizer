<?php


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService;

final class HashAnonymizer implements HashAnonymizerInterface
{
    public const PUNCTUATION = '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';//32

    public const NUMBERS = 'numbers';
    public const LETTERS = 'letters';
    public const SIGNS = 'signs';
    public const NUMBERS_0_255 = 'numbers_0_255';

    private array $stacks = [
        self::NUMBERS => null,
        self::LETTERS => null,
        self::SIGNS => null,
        self::NUMBERS_0_255 => null,
    ];

    private array $stackLengths = [
        self::NUMBERS => null,
        self::LETTERS => null,
        self::SIGNS => null,
        self::NUMBERS_0_255 => null,
    ];

    private array $cnt = [
        self::NUMBERS => -1,
        self::LETTERS => -1,
        self::SIGNS => -1,
        self::NUMBERS_0_255 => -1,
    ];

    private string $hash;

    public function __construct()
    {
    }

    private function resetStacks()
    {
        foreach ($this->stacks as $key => $value) {
            $this->stacks[$key] = null;
            $this->cnt[$key] = -1;
        }
    }

    public function initializeHashString(string $hash): void
    {
        $this->hash = $hash;
        $this->resetStacks();
    }

    public function getNextNumberBetween0And255(): string
    {
        return $this->getNextFromStack(self::NUMBERS_0_255);
    }

    public function getNextNumber(): string
    {
        return $this->getNextFromStack(self::NUMBERS);
    }

    public function getNextLetter(): string
    {
        return $this->getNextFromStack(self::LETTERS);
    }

    public function getNextPunctuation(): string
    {
        return $this->getNextFromStack(self::SIGNS);
    }

    public function isPunctuation($char): bool
    {
        return (strpos(self::PUNCTUATION, $char) !== false);
    }

    private function generateLetterStack(): void
    {
        $this->stacks[self::LETTERS] = str_replace(range(0, 9), '', base_convert($this->hash, 16, 36));
        if (strlen($this->stacks[self::LETTERS]) < 10) {
            //in the rare event when the sha hash has less than 10 chars
            $this->stacks[self::LETTERS] .= $this->numbersToLetters(base_convert($this->hash, 16, 10));
        }
        $this->stackLengths[self::LETTERS] = strlen($this->stacks[self::LETTERS]);
    }

    /**
     * @param string $numbers 64 number characters
     * @return string
     */
    private function numbersToLetters(string $numbers): string
    {
        $len = strlen($numbers); //len a
        if ($len === 1) {
            $numbers .= $numbers;
            $len = 2;
        }
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

    private function generateNumbersStack(): void
    {
        $this->stacks[self::NUMBERS] = base_convert($this->hash, 16, 10); //64 length
        $this->stackLengths[self::NUMBERS] = strlen($this->stacks[self::NUMBERS]);
    }

    private function generateNumbersBetween0and255Stack(): void
    {
        $this->stacks[self::NUMBERS_0_255] = [];
        $len = strlen($this->hash);
        $len = ($len > 10 ? 10 : $len); //Ten 0-255 numbers is usually enough. When its not, it will generate more times.
        for ($i = 0; $i <= $len - 2; $i += 2) {
            $this->stacks[self::NUMBERS_0_255][] = base_convert(substr($this->hash, $i, 2), 16, 10);
        }
        $this->stackLengths[self::NUMBERS_0_255] = count($this->stacks[self::NUMBERS_0_255]);
    }

    private function generateSignsStack(): void
    {
        $pool256 = str_split(self::PUNCTUATION);
        $intSeed = $this->hashToInt();

        mt_srand($intSeed, MT_RAND_MT19937);
        shuffle($pool256);

        $this->stacks[self::SIGNS] = implode('', $pool256);
        $this->stackLengths[self::SIGNS] = strlen($this->stacks[self::SIGNS]);
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
            if ($string === self::NUMBERS_0_255) {
                $this->generateNumbersBetween0and255Stack();
                $this->cnt[$string] = 0;
            }
        }


        if ($this->cnt[$string] >= $this->stackLengths[$string]) {
            $this->cnt[$string] = 0;
        }

        $ret = $this->stacks[$string][$this->cnt[$string]];
        $this->cnt[$string]++;
        return $ret;
    }

    private function hashToInt(): int
    {
        $intSeeds = [];
        $start = 0;
        while (($start <= strlen($this->hash) - 8) && ($start <= 32)) {
            $intSeeds[] = (int)base_convert(
                substr($this->hash, $start, 8),
                16,
                10
            );
            $start += 8;
        }
        return (int)floor(array_sum($intSeeds) / count($intSeeds));
    }
}
