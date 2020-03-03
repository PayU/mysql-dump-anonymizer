<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

class Serialized implements ValueAnonymizerInterface
{

    public function anonymize(Value $value, array $row, ConfigInterface $config): AnonymizedValue
    {
        $serializedString = $value->getUnEscapedValue();
        $array = unserialize($serializedString, ['allowed_classes' => false]);
        if (is_array($array)) {
            $anonymizedArray = $this->anonymizeArray($array, $config);
            return new AnonymizedValue(EscapeString::escape(serialize($anonymizedArray)));
        }

        return (new FreeText())->anonymize($value, $row, $config);
    }

    private function anonymizeArray(array $array, ConfigInterface $config): array
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
