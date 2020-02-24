<?php

namespace PayU\MysqlDumpAnonymizer\DataType;

interface InterfaceDataType {
    public function anonymize($value);
}