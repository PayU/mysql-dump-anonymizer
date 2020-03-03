<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractValueAnonymizerMocks extends TestCase
{

    /**
     * @param array $hashMeReturns
     * @return ConfigInterface|MockObject
     */
    protected function anonymizerConfigMock($hashMeReturns)
    {

        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMock();
        if (!empty($hashMeReturns)) {

            /**  @var StringHash|MockObject $stringHashMock */
            $stringHashMock = $this->getMockBuilder(StringHash::class)->getMock();
            $stringHashMock->method('hashMe')->willReturn(...$hashMeReturns);
            $configMock->method('getHashStringHelper')->willReturn($stringHashMock);
        }

        return $configMock;
    }
}
