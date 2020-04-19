<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ConfigReader;

use PayU\MysqlDumpAnonymizer\ValueAnonymizers\BankData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\BinaryData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\CardData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Credentials;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\DocumentData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Eav;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Email;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FileName;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Id;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\NoAnonymization;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\StringHashInterfaceSha256;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Ip;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\IpInt;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Json;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Phone;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Serialized;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Url;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Username;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\ValueAnonymizerInterface;

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

    /** @var ValueAnonymizerInterface[]  */
    private $instances = [];

    /**
     * @param string $string
     * @param array|null $constructArguments
     * @return ValueAnonymizerInterface
     */
    public function getValueAnonymizerClass(string $string, array $constructArguments) : ValueAnonymizerInterface
    {
        if (!empty($constructArguments)) {
            return new self::$valueAnonymizers[$string](...$constructArguments);
        }

        if (!array_key_exists($string, $this->instances)) {
            $this->instances[$string] = new self::$valueAnonymizers[$string](new StringHashInterfaceSha256());
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
