<?php
namespace PayU\MysqlDumpAnonymizer\Helper;

interface StringHash
{
    public function sha256($string, $rawOutput = false): string;
    public function hashMe($word, $anonymizePunctuation = false) : string;
}