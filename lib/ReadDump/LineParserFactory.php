<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ReadDump;

use RuntimeException;

final class LineParserFactory
{

    public const LINE_PARSER_MYSQL_DUMP = 'mysqldump';

    public function chooseLineParser($configString) : LineParserInterface
    {

        if ($configString === self::LINE_PARSER_MYSQL_DUMP) {
            return new MySqlDumpLineParser();
        }

        throw new RuntimeException('Invalid line parser config');
    }
}
