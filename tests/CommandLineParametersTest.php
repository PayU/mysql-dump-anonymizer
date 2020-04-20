<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests;

use PayU\MysqlDumpAnonymizer\Application\CommandLineParameters;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandLineParametersTest extends TestCase
{
    /**
     * @var \PayU\MysqlDumpAnonymizer\Application\\PayU\MysqlDumpAnonymizer\Application\CommandLineParameters|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}