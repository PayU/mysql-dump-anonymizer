<?php

namespace PayU\MysqlDumpAnonymizer\DataType;




use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Services\EscapeString;
use PayU\MysqlDumpAnonymizer\Services\StringHash;

class Json implements InterfaceDataType
{

    public function anonymize(Value $value): Value
    {
        $array = json_decode($value->getUnEscapedValue(), true);

        $value->setRawValue(EscapeString::escape(json_encode($this->anonymizeArray($array))));

        return $value;
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