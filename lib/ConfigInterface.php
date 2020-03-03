<?php


namespace PayU\MysqlDumpAnonymizer;


use PayU\MysqlDumpAnonymizer\Helper\StringHash;

interface ConfigInterface
{
    public function getHashStringHelper() : StringHash;

}