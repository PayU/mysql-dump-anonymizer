<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;


use JsonException;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class Json implements ValueAnonymizerInterface
{

    //TODO use JSON_THROW
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $jsonString = str_replace(["\r", "\n"], ["\\r", "\\n"], $value->getUnEscapedValue());

        try {
            $array = json_decode($jsonString, true, 512);

            if (is_array($array)) {
                return new AnonymizedValue(EscapeString::escape(json_encode($this->anonymizeArray($array, $config))));
            }

        } catch (JsonException $e) {

        }

        return (new FreeText())->anonymize($value, $row, $config);

    }

    private function anonymizeArray(array $array, Config $config): array
    {
        $ret = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $ret[$key] = $this->anonymizeArray($value, $config);
            } else {
                $ret[$key] = $config->getHashStringHelper()->hashMe($value);
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