<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\ConfigInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\BankData;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\BinaryData;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\CardData;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Credentials;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\DocumentData;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Eav;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Email;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\FileName;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\FreeText;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Id;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\NoAnonymization;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Ip;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\IpInt;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Json;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Phone;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Serialized;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Url;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\Username;

final class ValueAnonymizerFactory
{

    /** @var array  */
    private static $valueAnonymizers = [
        'FreeText' => FreeText::class,
        'BankData' => BankData::class,
        'BinaryData' => BinaryData::class,
        'CardData' => CardData::class,
        'Credentials' => Credentials::class,
        'DocumentData' => DocumentData::class,
        'Email' => Email::class,
        'FileName' => FileName::class,
        'Id' => Id::class,
        'Ip' => Ip::class,
        'IpInt' => IpInt::class,
        'Json' => Json::class,
        'Phone' => Phone::class,
        'SensitiveFreeText' => SensitiveFreeText::class,
        'Serialized' => Serialized::class,
        'Url' => Url::class,
        'Username' => Username::class,
        'Eav' => Eav::class,
        'NoAnonymization' => NoAnonymization::class
    ];

    public const NO_ANONYMIZATION = 'NoAnonymization';

    /** @var \PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface[]  */
    private $instances = [];

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $string
     * @param array|null $constructArguments
     * @return \PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface
     */
    public function getValueAnonymizerClass(string $string, array $constructArguments) : \PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizerInterface
    {
        if (!empty($constructArguments)) {
            return new self::$valueAnonymizers[$string](...$constructArguments);
        }

        if (!array_key_exists($string, $this->instances)) {
            $this->instances[$string] = new self::$valueAnonymizers[$string]($this->config);
        }

        return $this->instances[$string];
    }

    public function valueAnonymizerExists(string $string) : bool
    {
        return array_key_exists($string, self::$valueAnonymizers);
    }

    public static function getValueAnonymizers() : array
    {
        return array_map(static function ($value) {
            return substr(strrchr($value, '\\'), 1);
        }, self::$valueAnonymizers);
    }
}
