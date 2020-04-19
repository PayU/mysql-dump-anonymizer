<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FileName;
use PHPUnit\Framework\MockObject\MockObject;

class FileNameTest extends AbstractValueAnonymizerMocks
{

    public function testAnonymize(): void
    {
        $input = '/admin/log_monitor.php';

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock([
            '/odfeb/ymt_wecgccw'
        ]);

        $sut = new FileName($configMock);

        $val = new Value('\''.$input.'\'', $input, false );

        $actual = $sut->anonymize($val, []);

        $this->assertSame('\'/odfeb/ymt_wecgccw.php\'', $actual->getRawValue());
    }
}
