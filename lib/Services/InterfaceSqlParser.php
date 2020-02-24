<?php

namespace PayU\MysqlDumpAnonymizer\Services;

interface InterfaceSqlParser {

    public function getColumns($insertLine);

    public function getValues($insertLine);

}