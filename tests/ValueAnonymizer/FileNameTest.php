<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Tests\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FileName;
use PHPUnit\Framework\MockObject\MockObject;

class FileNameTest extends AbstractValueAnonymizerMocks
{
  /**
     * @var FileName
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new FileName();
    }

    public function testAnonymize()
    {
        $input = '/admin/log_monitor.php';

        /** @var ConfigInterface|MockObject $configMock */
        $configMock = $this->anonymizerConfigMock([
            '/odfeb/ymt_wecgccw'
        ]);

        $val = new Value('\''.$input.'\'', $input, false );

        $actual = $this->sut->anonymize($val, [], $configMock);

        $this->assertSame('\'/odfeb/ymt_wecgccw.php\'', $actual->getRawValue());
    }
}
