<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers;

use JsonException;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\ConfigInterface;

use PayU\MysqlDumpAnonymizer\ReadDump\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Json implements ValueAnonymizerInterface
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

        $jsonString = str_replace(["\r", "\n"], ["\\r", "\\n"], $value->getUnEscapedValue());

        try {
            $array = json_decode($jsonString, true, 512);

            if (is_array($array)) {
                return new AnonymizedValue(EscapeString::escape(
                    json_encode($this->anonymizeArray($array))
                ));
            }

            return (new FreeText($this->config))->anonymize($value, $row);

        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (JsonException $e) {

            return (new FreeText($this->config))->anonymize($value, $row);
        }
    }

    private function anonymizeArray(array $array): array
    {
        $ret = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $ret[$key] = $this->anonymizeArray($value);
            } else {
                $ret[$key] = $this->config->getHashStringHelper()->hashMe($value);
                if (is_int($value)) {
                    $ret[$key] = (int)$ret[$key];
                }
                if (is_float($value)) {
                    $ret[$key] = (float)$ret[$key];
                }
            }
        }
        return $ret;
    }
}
