<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\WriteDump;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;

final class MysqlLineDump implements LineDumpInterface
{
    private $rowContentGenerator;

    /**
     * MysqlLineDump constructor.
     * @param $rawValueExtracter
     * @param $rowContentGenerator
     */
    public function __construct()
    {
        $rawValueExtractor = static function ($value) {
            return $value->getRawValue();
        };
        $this->rowContentGenerator = static function ($row) use ($rawValueExtractor) {
            return implode(',', array_map($rawValueExtractor, $row));
        };
    }


    /**
     * @param string $table
     * @param array $columns
     * @param AnonymizedValue[][] $rows
     * @return string
     */
    public function rebuildInsertLine(string $table, array $columns, array $rows): string
    {
        return 'INSERT INTO `' . $table . '` (`'
            . implode('`, `', $columns)
            . '`) VALUES ('
            . implode('),(', array_map($this->rowContentGenerator, $rows))
            . ');'
            . "\n";
    }
}
