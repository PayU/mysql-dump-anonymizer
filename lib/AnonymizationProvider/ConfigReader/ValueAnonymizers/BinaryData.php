<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\ReadDump\Value;

final class BinaryData implements ValueAnonymizerInterface
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
        if ((empty($value->getUnEscapedValue())) || ($value->isExpression() === false)) {
            return new AnonymizedValue('\'\'');
        }

        $hexExpression = substr($value->getUnEscapedValue(), 2);
        $i = 0;
        $anonymizedHexExpression = '';
        do {
            $part = substr($hexExpression, $i, 64);
            $anonymizedHexExpression .= $this->config->getHashStringHelper()->sha256($part);
            $i += 64;

            //TODO see how big the blob can be - maybe config ?
            if ($i >= (64*30000)) {
                break;
            }
        } while ($i < strlen($hexExpression));

        return new AnonymizedValue('0x'.$anonymizedHexExpression);
    }
}
