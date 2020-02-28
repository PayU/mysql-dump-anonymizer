<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

class Serialized implements InterfaceDataType
{

    public function anonymize(Value $value): AnonymizedValue
    {
        return new AnonymizedValue($value->getRawValue());
    }
}