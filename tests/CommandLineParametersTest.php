<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests;

use PayU\MysqlDumpAnonymizer\CommandLineParameters;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandLineParametersTest extends TestCase
{
    /**
     * @var CommandLineParameters|MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
    }

}