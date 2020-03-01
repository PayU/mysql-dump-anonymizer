<?php

namespace PayU\MysqlDumpAnonymizer\Helper;

class EscapeString
{

    public static function escape(string $string): string
    {
        return '\'' . addcslashes($string, "'\\\n") . '\'';
    }
}
