<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractValueAnonymizerMocks extends TestCase
{

    /**
     * @param array $hashMeReturns
     * @return \PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface|MockObject
     */
    protected function anonymizerConfigMock($hashMeReturns)
    {

        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        if (!empty($hashMeReturns)) {

            /**  @var \PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterface|MockObject $stringHashMock */
            $stringHashMock = $this->getMockBuilder(StringHashInterface::class)->getMock();
            $stringHashMock->method('hashMe')->willReturn(...$hashMeReturns);
            $configMock->method('getHashStringHelper')->willReturn($stringHashMock);
        }

        return $configMock;
    }
}
