<?php

namespace PayU\MysqlDumpAnonymizer;

interface InsertLineParser
{
    public function parse(string $line): InsertLine;
}
