<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Services;

interface InterfaceSqlParser
{

    public function getColumns($insertLine);

    public function getValues($insertLine);
}
