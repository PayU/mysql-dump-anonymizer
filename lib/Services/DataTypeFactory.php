<?php
namespace PayU\MysqlDumpAnonymizer\Services;

use PayU\MysqlDumpAnonymizer\ValueAnonymizer\BankData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\BinaryData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\CardData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Credentials;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Date;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\DocumentData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Eav;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Email;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FileName;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Id;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\NoAnonymization;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Ip;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\IpInt;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Json;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Phone;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Serialized;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Url;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Username;

final class DataTypeFactory {

    /** @var array  */
    private static $dataTypes = [
        'FreeText' => FreeText::class,
        'BankData' => BankData::class,
        'BinaryData' => BinaryData::class,
        'CardData' => CardData::class,
        'Credentials' => Credentials::class,
        'Date' => Date::class,
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
    public function getDataTypeClass( string $string, array $constructArguments) : ValueAnonymizerInterface {
        if (!empty($constructArguments)) {
            return new self::$dataTypes[$string](...$constructArguments);
        }

        if (!array_key_exists($string, $this->instances)) {
            $this->instances[$string] = new self::$dataTypes[$string]();
        }

       return $this->instances[$string];
    }

    public function dataTypeExists(string $string) : bool {
        return array_key_exists($string, self::$dataTypes);
    }

    public static function getDataTypes() : array {
        return array_map(static function ($value) {
            return substr(strrchr($value, '\\'), 1);
        }, self::$dataTypes);
    }

}