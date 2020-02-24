<?php

namespace PayU\MysqlDumpAnonymizer;
//TODO delete?
interface InsertLineDumper
{
    public function dump(InsertLine $insertLine): string;
}
