<?php

namespace PayU\MysqlDumpAnonymizer\Entity;

use PayU\MysqlDumpAnonymizer\DataType\BinaryData;
use PayU\MysqlDumpAnonymizer\DataType\CardData;
use PayU\MysqlDumpAnonymizer\DataType\Credentials;
use PayU\MysqlDumpAnonymizer\DataType\Date;
use PayU\MysqlDumpAnonymizer\DataType\DocumentData;
use PayU\MysqlDumpAnonymizer\DataType\Email;
use PayU\MysqlDumpAnonymizer\DataType\FileName;
use PayU\MysqlDumpAnonymizer\DataType\FreeText;
use PayU\MysqlDumpAnonymizer\DataType\Id;
use PayU\MysqlDumpAnonymizer\DataType\Ip;
use PayU\MysqlDumpAnonymizer\DataType\IpInt;
use PayU\MysqlDumpAnonymizer\DataType\Json;
use PayU\MysqlDumpAnonymizer\DataType\Phone;
use PayU\MysqlDumpAnonymizer\DataType\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\DataType\Serialized;
use PayU\MysqlDumpAnonymizer\DataType\Url;
use PayU\MysqlDumpAnonymizer\DataType\Username;

final class DataTypes {

    public const BANK_DATA = ['BankData', SensitiveFreeText::class];
    public const BINARY_DATA = ['BinaryData', BinaryData::class];
    public const CARD_DATA = ['CardData', CardData::class];
    public const CREDENTIALS = ['Credentials', Credentials::class];
    public const DATE = ['Date', Date::class];
    public const DOCUMENT_DATA = ['DocumentData', DocumentData::class];
    public const EMAIL = ['Email', Email::class];
    public const FILENAME = ['FileName', FileName::class];
    public const FREE_TEXT = ['FreeText', FreeText::class];
    public const ID = ['Id', Id::class];
    public const IP = ['Ip', Ip::class];
    public const IP_INT = ['IpInt', IpInt::class];
    public const JSON = ['Json', Json::class];
    public const PHONE = ['Phone', Phone::class];
    public const SENSITIVE_FREE_TEXT = ['SensitiveFreeText', SensitiveFreeText::class];
    public const SERIALIZED = ['Serialized', Serialized::class];
    public const URL = ['Url', Url::class];
    public const USERNAME = ['Username', Username::class];



}