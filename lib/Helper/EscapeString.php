<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Helper;

final class EscapeString
{

    public static function escape(string $string): string
    {
        return '\'' . addcslashes($string, "'\\\n") . '\'';
    }
}
