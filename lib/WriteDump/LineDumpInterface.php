<?php


namespace PayU\MysqlDumpAnonymizer\WriteDump;


use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue;

interface LineDumpInterface
{
    /**
     * @param string $table
     * @param array $columns
     * @param \PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $values) : string;
}