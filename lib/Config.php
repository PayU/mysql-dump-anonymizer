<?php

namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Helper\StringHash;

class Config
{

    /**
     * @var StringHash
     */
    private $hashStringHelper;

    public function __construct()
    {
        $this->hashStringHelper = new StringHash(hash('sha256', microtime(1)));
    }

    public function getHashStringHelper() : StringHash
    {
        return $this->hashStringHelper;
    }
}
