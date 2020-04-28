<?php
namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService;

interface StringHashInterface
{
    public function sha256($string, $rawOutput = false): string;
    public function hashKeepFormat($string, $anonymizePunctuation = false) : string;
    public function hashIpAddressString(string $string) : string;
}
