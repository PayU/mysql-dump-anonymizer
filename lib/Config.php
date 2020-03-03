<?php

declare(strict_types=1);


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
        $this->hashStringHelper = new StringHash(hash('sha256', (string)microtime(true)));
    }

    public function getHashStringHelper() : StringHash
    {
        return $this->hashStringHelper;
    }
}
