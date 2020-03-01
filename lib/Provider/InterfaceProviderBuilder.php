<?php

namespace PayU\MysqlDumpAnonymizer\Provider;

use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;

interface InterfaceProviderBuilder {

    /**
     * @throws ConfigValidationException
     */
    public function validate() : void;

    public function buildProvider() : AnonymizationProviderInterface;

}