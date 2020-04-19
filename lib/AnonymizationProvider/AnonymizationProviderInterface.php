<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider;

use PayU\MysqlDumpAnonymizer\Entity\ValueAnonymizerInterface;

interface AnonymizationProviderInterface
{

    public function getTableAction($table);

    public function getAnonymizationFor($table, $column) : ValueAnonymizerInterface;

    public function isNoAnonymization(ValueAnonymizerInterface $valueAnonymizer) : bool;

}
