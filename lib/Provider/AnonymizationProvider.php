<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Provider;

use PayU\MysqlDumpAnonymizer\ValueAnonymizer\ValueAnonymizerInterface;

final class AnonymizationProvider implements AnonymizationProviderInterface
{

    /** @var array  */
    private $tablesAction;

    /** @var ValueAnonymizerInterface[][]  */
    private $tableColumnsAnonymizationProvider;

    /** @var  int */
    private $tableNotFoundAction;

    /**
     * @var ValueAnonymizerInterface
     */
    private $tableColumnNotFoundAnonymizer;

    /**
     * AnonymizationProvider constructor.
     * @param array $tablesAction
     * @param int $tableNotFoundAction
     * @param array $tableColumnsAnonymizationProvider
     * @param ValueAnonymizerInterface $notFoundAnonymizer
     */
    public function __construct(
        $tablesAction,
        $tableNotFoundAction,
        array $tableColumnsAnonymizationProvider,
        ValueAnonymizerInterface $notFoundAnonymizer
    ) {
        $this->tablesAction = $tablesAction;
        $this->tableNotFoundAction = $tableNotFoundAction;
        $this->tableColumnsAnonymizationProvider = $tableColumnsAnonymizationProvider;
        $this->tableColumnNotFoundAnonymizer = $notFoundAnonymizer;
    }

    public function getTableAction($table)
    {
        if (array_key_exists($table, $this->tablesAction)) {
            return $this->tablesAction[$table];
        }
        return $this->tableNotFoundAction;
    }

    public function getAnonymizationFor($table, $column) : ValueAnonymizerInterface
    {
        if (array_key_exists($table, $this->tableColumnsAnonymizationProvider)
            && array_key_exists($column, $this->tableColumnsAnonymizationProvider[$table])
        ) {
            return $this->tableColumnsAnonymizationProvider[$table][$column];
        }

        return $this->tableColumnNotFoundAnonymizer;
    }
}
