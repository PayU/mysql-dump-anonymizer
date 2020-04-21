<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\WriteDump;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;

interface LineDumpInterface
{
    /**
     * @param string $table
     * @param array $columns
     * @param AnonymizedValue[][] $values
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $values) : string;
}