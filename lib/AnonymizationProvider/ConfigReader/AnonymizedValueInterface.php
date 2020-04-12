<?php


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader;


interface AnonymizedValueInterface
{
    public function getRawValue(): string;
}