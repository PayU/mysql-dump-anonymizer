<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class IpInt implements ValueAnonymizerInterface
{
    private StringHashInterface $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        $ip = (new Ip($this->stringHash))->anonymize($value, $row)->getRawValue();
        return AnonymizedValue::fromRawValue((string)ip2long(substr($ip, 1, -1)));
    }
}
