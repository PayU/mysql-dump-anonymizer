<?php

namespace PayU\MysqlDumpAnonymizer\DataType;




use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;
use PayU\MysqlDumpAnonymizer\Services\StringHash;
use RuntimeException;

class Json implements InterfaceDataType
{

    public function anonymize(Value $value): AnonymizedValue
    {
        if ($value->isExpression()) {
            return new AnonymizedValue($value->getRawValue());
        }

        $jsonString = str_replace(["\r","\n"], ["\\r","\\n"], $value->getUnEscapedValue());

        $array = json_decode($jsonString, true);

        if (is_array($array)) {
            $value->setRawValue(EscapeString::escape(json_encode($this->anonymizeArray($array))));
        }else{
            //TODO do something else ?
            throw new RuntimeException('No support for non array jsons');
        }
        return new AnonymizedValue(EscapeString::escape(json_encode($this->anonymizeArray($array))));
    }

    private function anonymizeArray(array $array) {
        $ret = [];
        foreach ($array as $key=>$value) {
            if (is_array($value)) {
                $ret[$key] = $this->anonymizeArray($value);
            }else {
                $ret[$key] = (new StringHash('the@salt--'))->hashMe($value);
            }
        }
        return $ret;

    }

}