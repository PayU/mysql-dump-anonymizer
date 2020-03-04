<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Url implements ValueAnonymizerInterface
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

        $unescapedURL = $value->getUnEscapedValue();

        $scheme = parse_url($unescapedURL, PHP_URL_SCHEME);

        if ($scheme !== null) {
            $host = substr($unescapedURL, strlen((string)$scheme) + 3);
            $anonymizedHost = $this->config->getHashStringHelper()->hashMe($host);
            $anonymizedUrl = $scheme . '://' . $anonymizedHost;
        } else {
            $anonymizedUrl = $this->config->getHashStringHelper()->hashMe($unescapedURL);
        }

        return new AnonymizedValue(EscapeString::escape($anonymizedUrl));
    }
}
