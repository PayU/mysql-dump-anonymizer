<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\Helper\StringHashInterface;
use PayU\MysqlDumpAnonymizer\Helper\StringHashInterfaceSha256;

final class Config implements ConfigInterface
{

    /**
     * @var StringHashInterface
     */
    private $hashStringHelper;

    public function __construct()
    {
        $this->hashStringHelper = new StringHashInterfaceSha256(hash('sha256', (string)microtime(true)));
    }

    public function getHashStringHelper() : StringHashInterface
    {
        return $this->hashStringHelper;
    }
}
