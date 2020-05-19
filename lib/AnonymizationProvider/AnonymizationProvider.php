<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider;

final class AnonymizationProvider implements AnonymizationProviderInterface
{
    public const NO_ANONYMIZATION = 'NoAnonymization';

    /** @var array  */
    private array $tablesAction;

    /** @var ValueAnonymizerInterface[] */
    private array $tableColumnsAnonymizationProvider;

    /** @var int */
    private int $tableNotFoundAction;

    /**
     * @var ValueAnonymizerInterface
     */
    private ValueAnonymizerInterface $tableColumnNotFoundAnonymizer;

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
        if (isset($this->tablesAction[$table])) {
            return $this->tablesAction[$table];
        }
        return $this->tableNotFoundAction;
    }

    public function getAnonymizationFor($table, $column) : ValueAnonymizerInterface
    {
        if (isset($this->tableColumnsAnonymizationProvider[$table][$column])
        ) {
            return $this->tableColumnsAnonymizationProvider[$table][$column];
        }

        return $this->tableColumnNotFoundAnonymizer;
    }

    public function isAnonymization(ValueAnonymizerInterface $valueAnonymizer): bool
    {
        $className = get_class($valueAnonymizer);
        if ($shortClassName = strrchr($className, "\\")) {
            $className = substr($shortClassName, 1);
        }
        return $className !== self::NO_ANONYMIZATION;
    }
}
