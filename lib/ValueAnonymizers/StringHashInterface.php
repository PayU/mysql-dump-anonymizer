<?php
namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

interface StringHashInterface
{
    public function sha256($string, $rawOutput = false): string;
    public function hashMe($word, $anonymizePunctuation = false) : string;
}