<?php


namespace PayU\MysqlDumpAnonymizer\WriteDump;



interface LineDumpInterface
{
    /**
     * @param string $table
     * @param array $columns
     * @param \PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $values) : string;
}