<?php


namespace PayU\MysqlDumpAnonymizer\LineDump;


use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;

interface LineDump
{
    /**
     * @param string $table
     * @param array $columns
     * @param AnonymizedValue[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $values) : string;
}