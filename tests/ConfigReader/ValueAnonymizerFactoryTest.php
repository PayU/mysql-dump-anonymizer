<?php

namespace PayU\MysqlDumpAnonymizer\Tests\ConfigReader;

use PayU\MysqlDumpAnonymizer\ConfigReader\ValueAnonymizerFactory;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Email;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FreeText;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionObject;

class ValueAnonymizerFactoryTest extends TestCase
{

    /**
     * @var ValueAnonymizerFactory
     */
    private ValueAnonymizerFactory $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new ValueAnonymizerFactory();
    }

    /**
     * @dataProvider providerValueAnonymizers
     * @param string $key
     * @param string $classString
     * @param array $constructArgs
     * @throws ReflectionException
     */
    public function testGetValueAnonymizerClassAll($key, $classString, $constructArgs): void
    {
        $this->resetPrivateInstances();
        $actual = $this->sut->getValueAnonymizerClass($key, $constructArgs);
        $this->assertInstanceOf($classString, $actual);
        if (!empty($constructArgs)) {
            $this->assertCount(0, $this->getPrivateInstances(), "for $classString");
        }else{
            $this->assertCount(1, $this->getPrivateInstances(), "for $classString");
        }
    }

    /**
     * @throws ReflectionException
     */
    public function testGetValueAnonymizerClassMultipleCallsSame(): void
    {
        $this->resetPrivateInstances();
        $actual1 = $this->sut->getValueAnonymizerClass('FreeText', []);
        $actual2 = $this->sut->getValueAnonymizerClass('FreeText', []);

        $this->assertInstanceOf(FreeText::class, $actual1);
        $this->assertInstanceOf(FreeText::class, $actual2);

        $this->assertCount(1, $this->getPrivateInstances());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetValueAnonymizerClassDiff(): void
    {
        $this->resetPrivateInstances();
        $actual1 = $this->sut->getValueAnonymizerClass('FreeText', []);
        $actual2 = $this->sut->getValueAnonymizerClass('Email', []);

        $this->assertInstanceOf(FreeText::class, $actual1);
        $this->assertInstanceOf(Email::class, $actual2);

        $this->assertCount(2, $this->getPrivateInstances());
    }

    /** @dataProvider providerValueAnonymizersExist
     * @param string $string
     */
    public function testValueAnonymizerExistsTrue($string): void
    {
        $actual = $this->sut->valueAnonymizerExists($string);
        $this->assertTrue($actual, "for $string");
    }

    public function testValueAnonymizerExistsFalse(): void
    {
        $actual = $this->sut->valueAnonymizerExists('does-not-exist');
        $this->assertFalse($actual);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function providerValueAnonymizers(): array
    {
        $anonymizers = $this->getPrivateValueAnonymizers();
        $ret = [];
        foreach ($anonymizers as $key=>$classString) {
            if ($key === 'Eav') {
                $ret[] = [$key, $classString, ['string',['array']]];
            }else{
                $ret[] = [$key, $classString, []];
            }
        }
        return $ret;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function providerValueAnonymizersExist(): array
    {
        $anonymizers = $this->getPrivateValueAnonymizers();
        $ret = [];
        foreach ($anonymizers as $key=>$classString) {
            $ret[] = [$key];
        }
        return $ret;
    }

    /**
     * @throws ReflectionException
     */
    private function resetPrivateInstances() : void
    {
        $refObject   = new ReflectionObject( $this->sut );
        $refProperty = $refObject->getProperty( 'instances' );
        $refProperty->setAccessible( true );
        $refProperty->setValue($this->sut, []);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function getPrivateInstances() : array
    {
        $refObject   = new ReflectionObject( $this->sut );
        $refProperty = $refObject->getProperty( 'instances' );
        $refProperty->setAccessible( true );
        return $refProperty->getValue($this->sut);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function getPrivateValueAnonymizers() : array
    {
        $refObject   = new ReflectionObject( new ValueAnonymizerFactory() );
        $refProperty = $refObject->getProperty( 'valueAnonymizers' );
        $refProperty->setAccessible( true );
        return $refProperty->getValue();
    }

}
