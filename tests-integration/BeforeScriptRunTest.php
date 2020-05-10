<?php /** @noinspection SqlResolve */

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\TestsIntegration;

use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class BeforeScriptRunTest extends TestCase
{
    public const TEXT_SOURCE = "\\next-new-line\nnext-new-line-with-r\r\nnext-is-tab\tnext-is-single-and-double-quote'\"";
    public const MULTI_BYTE_CHAR = 'ü';
    public const SOURCE_ID_1 = 100;
    public const SOURCE_ID_2 = 201;

    public static PDO $source;

    public function setUp(): void
    {
    }

    public static function setUpBeforeClass(): void
    {
        $dsnSource = getenv('DSN_SOURCE');
        if (empty($dsnSource)) {
            throw new RuntimeException('Empty environment variable DSN_SOURCE');
        }
        self::$source = new PDO($dsnSource, 'root', '');
        self::$source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function testPrepareJsonSource(): void
    {
        //key1 and key2-1 must have the same input value
        $json = json_encode([
            'key1' => self::TEXT_SOURCE.self::MULTI_BYTE_CHAR,
            'key2' => [
                'key2-1' => self::TEXT_SOURCE.self::MULTI_BYTE_CHAR,
            ],
        ], JSON_THROW_ON_ERROR);

        $data = [
            'id' => self::SOURCE_ID_1,
            'fname' => 'John',
            'json' => $json,
            'comment' => self::TEXT_SOURCE.self::MULTI_BYTE_CHAR
        ];

        $query = 'INSERT INTO example_1 SET id=:id, fname=:fname, json=:json, dated=NOW(), comment=:comment';
        $statement = self::$source->prepare($query);
        $executed1 = $statement->execute($data);

        $this->assertTrue($executed1);
    }

    public function testPrepareSerializeSource(): void
    {
        $serialize = serialize([
            'key1' => self::TEXT_SOURCE,
            'key2' => [
                'key2-1' => self::TEXT_SOURCE,
            ],
        ]);

        $data = [
            'id' => self::SOURCE_ID_2,
            'serialized' => $serialize,
        ];

        $query = 'INSERT INTO all_allow_null SET `PrimaryKey` = :id, 
            `BankData` = NULL,
            `BinaryData` = NULL,
            `CardData` = NULL,
            `Credentials` = NULL,
            `Date` = NULL,
            `DocumentData` = NULL,
            `Email` = NULL,
            `FileName` = NULL,
            `FreeText` = NULL,
            `Id` = NULL,
            `Ip` = NULL,
            `IpInt` = NULL,
            `Json` = NULL,
            `Phone` = NULL,
            `SensitiveFreeText` = NULL,
            `Url` = NULL,
            `Username` = NULL, 
            `Serialized` = :serialized';

        $statement = self::$source->prepare($query);
        $executed1 = $statement->execute($data);
        $this->assertTrue($executed1);
    }
}
