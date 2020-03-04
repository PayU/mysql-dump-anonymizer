<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class FreeText implements ValueAnonymizerInterface
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

        $string = $value->getUnEscapedValue();
        if (strlen($string) < 12) {
            $anonymizedString = $this->config->getHashStringHelper()->hashMe(
                substr($this->config->getHashStringHelper()->sha256($string), 0 ,12)
            );
        }else{
            $anonymizedString = $this->config->getHashStringHelper()->hashMe($value->getUnEscapedValue());
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedString));
    }
}
