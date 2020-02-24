<?php

namespace PayU\MysqlDumpAnonymizer;

//TODO delete?
interface InsertLineParser
{
    public function parse(string $line): InsertLine;
}
