<?php


namespace PayU\MysqlDumpAnonymizer;


use PayU\MysqlDumpAnonymizer\Helper\StringHashInterface;

interface ConfigInterface
{
    public function getHashStringHelper() : StringHashInterface;

}