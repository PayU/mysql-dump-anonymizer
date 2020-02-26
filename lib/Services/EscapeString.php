<?php

namespace PayU\MysqlDumpAnonymizer\Services;


class EscapeString {

    public static function escape(string $string) {
        return '\'' . addcslashes($string, "'\\\n") . '\'';
    }

}