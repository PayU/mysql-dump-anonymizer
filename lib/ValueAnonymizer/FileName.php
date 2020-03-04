<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class FileName implements ValueAnonymizerInterface
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

        $nameWithoutExtension = substr($unescapedValue, 0, (int)strrpos($unescapedValue, '.'));
        $extension = substr($unescapedValue, strrpos($unescapedValue, '.') + 1);

        $anonymizedNameWithoutExtension = $this->config->getHashStringHelper()->hashMe($nameWithoutExtension);

        return new AnonymizedValue(EscapeString::escape($anonymizedNameWithoutExtension . '.'.$extension));
    }
}
