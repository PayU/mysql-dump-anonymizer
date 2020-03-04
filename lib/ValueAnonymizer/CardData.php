<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class CardData implements ValueAnonymizerInterface
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

        $anonymizedEscapedValue = $this->config->getHashStringHelper()->hashMe($value->getUnEscapedValue());

        return new AnonymizedValue(EscapeString::escape($anonymizedEscapedValue));
    }
}
