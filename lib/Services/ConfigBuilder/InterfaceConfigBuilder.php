<?php

namespace PayU\MysqlDumpAnonymizer\Services\ConfigBuilder;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationConfig;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;

interface InterfaceConfigBuilder {

    /**
     * @throws ConfigValidationException
     */
    public function validate() : void;

    public function buildConfig() : AnonymizationConfig;

}