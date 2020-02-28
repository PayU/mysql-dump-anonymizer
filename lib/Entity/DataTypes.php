<?php
//TODO factory this
namespace PayU\MysqlDumpAnonymizer\Entity;

use PayU\MysqlDumpAnonymizer\ValueAnonymizer\BankData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\BinaryData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\CardData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Credentials;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Date;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\DocumentData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Email;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FileName;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\FreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Id;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\InterfaceDataType;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Ip;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\IpInt;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Json;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Phone;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Serialized;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Url;
use PayU\MysqlDumpAnonymizer\ValueAnonymizer\Username;

final class DataTypes {

    /** @var array  */
    private static $dataTypes = [
        'BankData' => BankData::class,
        'BinaryData' => BinaryData::class,
        'CardData' => CardData::class,
        'Credentials' => Credentials::class,
        'Date' => Date::class,
        'DocumentData' => DocumentData::class,
        'Email' => Email::class,
        'FileName' => FileName::class,
        'FreeText' => FreeText::class,
        'Id' => Id::class,
        'Ip' => Ip::class,
        'IpInt' => IpInt::class,
        'Json' => Json::class,
        'Phone' => Phone::class,
        'SensitiveFreeText' => SensitiveFreeText::class,
        'Serialized' => Serialized::class,
        'Url' => Url::class,
        'Username' => Username::class,
    ];

    /**
     * @param string $string
     * @return InterfaceDataType
     */
    public function getDataTypeClass(string $string) : InterfaceDataType {
        //TODO eav maybe here like
        /*
        if ($string = 'Eav') {
            return (new self::$dataTypes[$string]);
        }
        */
       return (new self::$dataTypes[$string]);
    }

    public function dataTypeExists(string $string) : bool {
        return array_key_exists($string, self::$dataTypes);
    }

}