<?php
//TODO factory this
namespace PayU\MysqlDumpAnonymizer\Entity;

use PayU\MysqlDumpAnonymizer\DataType\BankData;
use PayU\MysqlDumpAnonymizer\DataType\BinaryData;
use PayU\MysqlDumpAnonymizer\DataType\CardData;
use PayU\MysqlDumpAnonymizer\DataType\Credentials;
use PayU\MysqlDumpAnonymizer\DataType\Date;
use PayU\MysqlDumpAnonymizer\DataType\DocumentData;
use PayU\MysqlDumpAnonymizer\DataType\Email;
use PayU\MysqlDumpAnonymizer\DataType\FileName;
use PayU\MysqlDumpAnonymizer\DataType\FreeText;
use PayU\MysqlDumpAnonymizer\DataType\Id;
use PayU\MysqlDumpAnonymizer\DataType\InterfaceDataType;
use PayU\MysqlDumpAnonymizer\DataType\Ip;
use PayU\MysqlDumpAnonymizer\DataType\IpInt;
use PayU\MysqlDumpAnonymizer\DataType\Json;
use PayU\MysqlDumpAnonymizer\DataType\Phone;
use PayU\MysqlDumpAnonymizer\DataType\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\DataType\Serialized;
use PayU\MysqlDumpAnonymizer\DataType\Url;
use PayU\MysqlDumpAnonymizer\DataType\Username;

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