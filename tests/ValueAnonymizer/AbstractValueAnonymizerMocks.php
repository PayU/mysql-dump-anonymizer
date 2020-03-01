<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Config;
use PayU\MysqlDumpAnonymizer\Helper\StringHash;
use PHPUnit\Framework\TestCase;

abstract class AbstractValueAnonymizerMocks extends TestCase
{

    protected function anonymizerConfigMock($hashMeReturns)
    {

        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        if (!empty($hashMeReturns)) {
            $stringHashMock = $this->getMockBuilder(StringHash::class)->disableOriginalConstructor()->getMock();
            $stringHashMock->method('hashMe')->willReturn(...$hashMeReturns);
            $configMock->method('getHashStringHelper')->willReturn($stringHashMock);
        }

        return $configMock;
    }
}
