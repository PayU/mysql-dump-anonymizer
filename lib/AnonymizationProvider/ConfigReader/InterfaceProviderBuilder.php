<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\AnonymizationProviderInterface;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;

interface InterfaceProviderBuilder
{

    /**
     * @throws ConfigValidationException
     */
    public function validate() : void;

    public function buildProvider() : AnonymizationProviderInterface;
}
