<?php

namespace PayU\MysqlDumpAnonymizer\Services\ConfigBuilder;

use PayU\MysqlDumpAnonymizer\Provider\AnonymizationProvider;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;

interface InterfaceConfigBuilder {

    /**
     * @throws ConfigValidationException
     */
    public function validate() : void;

    public function buildConfig() : AnonymizationProvider;

}