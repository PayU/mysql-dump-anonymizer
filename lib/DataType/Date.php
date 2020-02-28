<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

class Date implements InterfaceDataType
{

    public function anonymize(Value $value): AnonymizedValue
    {
        return new AnonymizedValue($value->getRawValue());
    }
}