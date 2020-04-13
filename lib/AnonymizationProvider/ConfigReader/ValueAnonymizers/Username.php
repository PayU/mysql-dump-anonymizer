<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\ConfigInterface;

use PayU\MysqlDumpAnonymizer\ReadDump\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Username implements ValueAnonymizerInterface
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
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $unescapedValue = $value->getUnEscapedValue();

        //we want the anonymizedValue length to be at least 12
        if (strlen($unescapedValue) >= 12) {
            $anonymizedEscapedValue = $this->config->getHashStringHelper()->hashMe($unescapedValue);
        } else {
            $anonymizedEscapedValue = $this->config->getHashStringHelper()->hashMe(
                substr($this->config->getHashStringHelper()->sha256($unescapedValue), 0, 12)
            );
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}