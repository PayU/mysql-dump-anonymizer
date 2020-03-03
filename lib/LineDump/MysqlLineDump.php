<?php
namespace PayU\MysqlDumpAnonymizer\LineDump;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;

final class MysqlLineDump implements LineDump
{
    /**
     * @param string $table
     * @param array $columns
     * @param AnonymizedValue[][] $rows
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $rows): string
    {
        $dumpQuery = 'INSERT' . ' INTO `' . $table . '` (`';
        $dumpQuery .= implode('`, `', $columns);
        $dumpQuery .= '`) VALUES (';

        foreach ($rows as $row) {
            foreach ($row as $value) {
                $dumpQuery .= $value->getRawValue() . ', ';
            }
            $dumpQuery = substr($dumpQuery, 0, -2) . '), (';
        }
        return substr($dumpQuery, 0, -3) . ';';
    }
}