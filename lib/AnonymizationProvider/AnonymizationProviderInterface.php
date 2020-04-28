<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider;

interface AnonymizationProviderInterface
{

    public function getTableAction($table);

    public function getAnonymizationFor($table, $column) : ValueAnonymizerInterface;

    public function isAnonymization(ValueAnonymizerInterface $valueAnonymizer) : bool;
}
