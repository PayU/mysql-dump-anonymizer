<?php

namespace PayU\MysqlDumpAnonymizer;

interface InsertLineDumper
{
    public function dump(InsertLine $insertLine): string;
}
