<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Helper\EscapeString;

final class Serialized implements ValueAnonymizerInterface
{

    private StringHashInterface $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        $serializedString = $value->getUnEscapedValue();
        $array = unserialize($serializedString, ['allowed_classes' => false]);
        if (is_array($array)) {
            $anonymizedArray = $this->anonymizeArray($array);
            return new AnonymizedValue(EscapeString::escape(serialize($anonymizedArray)));
        }

        return (new FreeText($this->stringHash))->anonymize($value, $row);
    }

    private function anonymizeArray(array $array): array
    {
        $ret = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $ret[$key] = $this->anonymizeArray($value);
            } else {
                $ret[$key] = $this->stringHash->hashMe($value);
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
