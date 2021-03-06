<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ConfigReader;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\NoAnonymization;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\BankData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\BinaryData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\CardData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Credentials;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\DocumentData;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Eav;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Email;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FileName;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\FreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\HashAnonymizer;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Id;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashSha256;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Ip;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\IpInt;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Json;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Phone;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\SensitiveFreeText;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Serialized;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Url;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\Username;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;

final class ValueAnonymizerFactory implements ValueAnonymizerFactoryInterface
{

    private static array $valueAnonymizers = [
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

    /** @var ValueAnonymizerInterface[]  */
    private array $instances = [];

    /**
     * @param string $string
     * @param array $constructArguments
     * @return ValueAnonymizerInterface
     */
    public function getValueAnonymizerClass(string $string, array $constructArguments) : ValueAnonymizerInterface
    {
        if (!empty($constructArguments)) {
            return new self::$valueAnonymizers[$string](...$constructArguments);
        }

        if (!array_key_exists($string, $this->instances)) {
            $this->instances[$string] = new self::$valueAnonymizers[$string](new StringHashSha256(new HashAnonymizer()));
        }

        return $this->instances[$string];
    }

    public function valueAnonymizerExists(string $string) : bool
    {
        return array_key_exists($string, self::$valueAnonymizers);
    }
}
