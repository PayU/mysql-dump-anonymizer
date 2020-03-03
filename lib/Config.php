<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PayU\MysqlDumpAnonymizer\Helper\StringHashSha256;

final class Config implements ConfigInterface
{

    /**
     * @var StringHash
     */
    private $hashStringHelper;

    public function __construct()
    {
        $this->hashStringHelper = new StringHashSha256(hash('sha256', (string)microtime(true)));
    }

    public function getHashStringHelper() : StringHash
    {
        return $this->hashStringHelper;
    }
}
