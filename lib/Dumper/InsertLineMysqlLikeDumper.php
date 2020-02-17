<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Dumper;

use PayU\MysqlDumpAnonymizer\InsertLine;
use PayU\MysqlDumpAnonymizer\InsertLineDumper;
use PayU\MysqlDumpAnonymizer\Value;

class InsertLineMysqlLikeDumper implements InsertLineDumper
{
    public function dump(InsertLine $insertLine): string
    {
        $columnList = array_map(
            static function ($column) {
                return '`' . $column . '`';
            },
            $insertLine->getColumns()
        );

        $valuesList = array_map(
            static function ($values) {
                $valueList = array_map(
                    static function (Value $value) {
                        return $value->getRawValue();
                    },
                    $values
                );

                return '(' . implode(',', $valueList) . ')';
            },
            $insertLine->getValuesList()
        );

        return 'INSERT INTO `' . $insertLine->getTable() . '` (' . implode(', ', $columnList) . ') VALUES ' . implode(',', $valuesList) . ";\n";
    }

}
