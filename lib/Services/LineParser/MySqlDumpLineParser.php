<?php

namespace PayU\MysqlDumpAnonymizer\Services\LineParser;

use PayU\MysqlDumpAnonymizer\Entity\LineInfo;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use Generator;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;
use PhpMyAdmin\SqlParser\Components\IntoKeyword;
use PhpMyAdmin\SqlParser\Components\Array2d;
use RuntimeException;

class MySqlDumpLineParser implements InterfaceLineParser
{

    public const INSERT_START_STRING = 'INSERT INTO';
    public const INSERT_START_LENGTH = 11; //strlen(above)

    private const MARK_1 = '` (`';
    private const MARK_1_LEN = 4;
    private const MARK_2 = '`) VALUES (';
    private const COL_DELIM = '`, `';

    public function __construct()
    {
        Parser::$STATEMENT_PARSERS = [
            'INSERT' => InsertStatement::class,
        ];

        Parser::$KEYWORD_PARSERS = [
            'INTO' => [
                'class' => IntoKeyword::class,
                'field' => 'into',
            ],
            'VALUES' => [
                'class' => Array2d::class,
                'field' => 'values',
            ],
        ];
    }

    /**
     * @param string $line
     * @return LineInfo
     */
    public function lineInfo(string $line): LineInfo
    {
        $isInsert = (strpos($line, self::INSERT_START_STRING) === 0);

        $table = null;
        $columns = null;

        if ($isInsert) {
            $first = strpos($line, '`', 0) + 1;
            $table = substr($line, $first, strpos($line, '`', $first) - $first);

            $mark = strpos($line, self::MARK_1, self::INSERT_START_LENGTH);
            $markLastPost = $mark + self::MARK_1_LEN;
            $columnsString = substr($line, $markLastPost, strpos($line, self::MARK_2, $markLastPost) - $markLastPost);
            $columns = explode(self::COL_DELIM, $columnsString);
        }

        return new LineInfo($isInsert, $table, $columns);
    }


    /**
     * @param string $line
     * @return Generator
     */
    public function getRowFromInsertLine($line) : Generator
    {
        foreach ((new Parser($line))->statements as $statement) {
            /** @var InsertStatement $statement */
            foreach ($statement->values as $key => $values) {

                $return = [];
                foreach ($values->values as $columnIndex=>$value) {
                    $return[$columnIndex] = new Value($values->raw[$columnIndex], $value);
                }

                if (empty($return)) {
                    throw new RuntimeException('Empty values !');
                }

                yield $return;
            }
        }
    }


}