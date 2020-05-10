<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use JsonException;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashInterface;

final class Json implements ValueAnonymizerInterface
{

    private StringHashInterface $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }


    /**
     * @param Value $value
     * @param Value[] $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ($value->isExpression()) {
            return AnonymizedValue::fromOriginalValue($value);
        }

        $jsonString = str_replace(["\r", "\n", "\t"], ["\\r", "\\n", "\\t"], $value->getUnEscapedValue());

        try {
            $array = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            if (is_array($array)) {
                return AnonymizedValue::fromUnescapedValue(json_encode($this->anonymizeArray($array), JSON_THROW_ON_ERROR, 512));
            }
        } catch (JsonException $e) {
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
                $ret[$key] = $this->stringHash->hashKeepFormat($value);
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
