<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class IpInt implements ValueAnonymizerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        $ip = (new Ip($this->config))->anonymize($value, $row)->getRawValue();
        return new AnonymizedValue((string)ip2long(substr($ip, 1, -1)));
    }
}
