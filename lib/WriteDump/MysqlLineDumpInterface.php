<?php
namespace PayU\MysqlDumpAnonymizer\WriteDump;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\WriteDump\LineDumpInterface;

final class MysqlLineDumpInterface implements LineDumpInterface
{
    /**
     * @param string $table
     * @param array $columns
     * @param \PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue[][] $rows
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