<?php

namespace PayU\MysqlDumpAnonymizer\Application;

use InvalidArgumentException;

interface CommandLineParametersInterface {

    public function setCommandLineArguments(): void;

    /**
     * @throws InvalidArgumentException
     */
    public function validate(): void;

    public function help(): string;

    public function getConfigType(): string;

    public function getConfigFile(): string;

    public function getLineParser(): string;

    public function getEstimatedDumpSize(): int;

    public function isShowProgress(): bool;

}