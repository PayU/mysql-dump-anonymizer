<?php


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService;

interface HashAnonymizerInterface
{
    public function initializeHashString(string $string): void;

    public function getNextNumberBetween0And255(): string;

    public function getNextNumber(): string;

    public function getNextLetter(): string;

    public function getNextPunctuation(): string;

    public function isPunctuation($char): bool;
}
