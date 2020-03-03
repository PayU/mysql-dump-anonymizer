<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;

class IpInt implements ValueAnonymizerInterface
{

    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        $ip = (new Ip())->anonymize($value, $row, $config)->getRawValue();
        return new AnonymizedValue((string)ip2long(substr($ip, 1, -1)));
    }
}
