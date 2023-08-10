<?php /** @noinspection SqlResolve */

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\TestsIntegration;

use JsonException;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\HashAnonymizer;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AfterScriptRunTest extends TestCase
{
    public const SOURCE_ID_1 = 100;
    public const SOURCE_ID_2 = 201;

    public const MULTI_BYTE_CHAR = 'ü';

    public static PDO $destination;
    private static PDO $source;

    public function setUp(): void
    {
    }

    public static function setUpBeforeClass(): void
    {
        $dsnSource = getenv('DSN_SOURCE');
        if (empty($dsnSource)) {
            throw new RuntimeException('Empty environment variable DSN_SOURCE');
        }

        $dsnDestination = getenv('DSN_DESTINATION');
        if (empty($dsnDestination)) {
            throw new RuntimeException('Empty environment variable DSN_DESTINATION');
        }

        $dbUser = getenv('DB_USER');
        if (empty($dbUser)) {
            throw new RuntimeException('Empty environment variable DSN_USER');
        }

        $dbPass = getenv('DB_PASS');
        if ($dbPass === false) {
            throw new RuntimeException('No environment variable DB_PASS');
        }


        self::$source = new PDO($dsnSource, $dbUser, $dbPass);
        self::$destination = new PDO($dsnDestination, $dbUser, $dbPass);

        self::$source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$destination->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function testNullIsAnonymizedWithNull(): void
    {
        $fields = ['BankData',
            'BinaryData',
            'CardData',
            'Credentials',
            'Date',
            'DocumentData',
            'Email',
            'FileName',
            'FreeText',
            'Id',
            'Ip',
            'IpInt',
            'Json',
            'Phone',
            'SensitiveFreeText',
            'Serialized',
            'Url',
            'Username',
        ];

        foreach ($fields as $field) {
            $query = "SELECT PrimaryKey FROM all_allow_null WHERE $field IS NULL";
            $stmt = self::$source->query($query);
            $primaryKeys = [];
            while ($sourceRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $primaryKeys[] = $sourceRow['PrimaryKey'];
            }

            if (!empty($primaryKeys)) {
                $q2 = "SELECT PrimaryKey, $field FROM all_allow_null WHERE PrimaryKey IN (" . implode(',', $primaryKeys) . ')';
                $stmt2 = self::$destination->query($q2);
                while ($destRow = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $this->assertNull($destRow[$field], " $field PK {$destRow['PrimaryKey']} not null !");
                }
            } else {
                $this->assertTrue(false, "NO NULL test for $field !");
            }
        }
    }

    public function testDataIsAnonymized(): void
    {

        $fields = ['BankData',
            'BinaryData',
            'CardData',
            'Credentials',
            'DocumentData',
            'Email',
            'FileName',
            'FreeText',
            'Id',
            'Ip',
            'IpInt',
            'Json',
            'Phone',
            'SensitiveFreeText',
            'Serialized',
            'Url',
            'Username',
        ];

        $query = 'SELECT * FROM all_allow_null';
        $stmt = self::$source->query($query);
        $sourceRows = [];
        while ($sourceRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sourceRows[$sourceRow['PrimaryKey']] = $sourceRow;
        }

        if (!empty($sourceRows)) {
            $query2 = 'SELECT * FROM all_allow_null';
            $stmt2 = self::$destination->query($query2);
            while ($destRow = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $sourceRow = $sourceRows[$destRow['PrimaryKey']];

                $this->assertSame(
                    $sourceRow['Date'],
                    $destRow['Date'],
                    'Date is not the same at PK' . $destRow['PrimaryKey']
                );

                foreach ($fields as $field) {
                    if (($destRow[$field] !== null) && ($destRow[$field] !== '')) {
                        $this->assertNotSame(
                            $destRow[$field],
                            $sourceRow[$field],
                            "$field at PK{$destRow['PrimaryKey']} is the same !"
                        );
                    }
                }

                if ($destRow['Email'] !== null) {
                    $this->assertRegExp(
                        '/^.+\@\S+\.\S+$/',
                        $destRow['Email'],
                        "Email at PK{$destRow['PrimaryKey']} is not a valid email !"
                    );
                }

                if ($destRow['Url'] !== null) {
                    $this->assertNotFalse(
                        filter_var($destRow['Url'], FILTER_VALIDATE_URL),
                        "Url {$destRow['Url']} at PK{$destRow['PrimaryKey']} is not a valid url !"
                    );
                }

                if ($destRow['Ip'] !== null) {
                    $this->assertNotFalse(
                        filter_var($destRow['Ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4),
                        "Ip {$destRow['Ip']} at PK{$destRow['PrimaryKey']} is not a valid ipv4 !"
                    );
                }

                if (($destRow['FreeText'] !== null) && (strlen($sourceRow['FreeText']) >= 12)) {
                    $this->assertFreeTextKeepPunctuation(
                        $sourceRow['FreeText'],
                        $destRow['FreeText'],
                        'Punctuation for PK' . $destRow['PrimaryKey'] . ':'
                    );

                    //lowercase characters are anonymized with other lowercase characters
                    $this->assertFreeTextCharacters(
                        $sourceRow['FreeText'],
                        $destRow['FreeText'],
                        range('a', 'z'),
                        'Lowercase for for PK' . $destRow['PrimaryKey'] . ':'
                    );

                    //uppercase characters are anonymized with other uppercase characters
                    $this->assertFreeTextCharacters(
                        $sourceRow['FreeText'],
                        $destRow['FreeText'],
                        range('A', 'Z'),
                        'Uppercase for PK' . $destRow['PrimaryKey'] . ':'
                    );

                    //numbers are replaced with other numbers
                    $this->assertFreeTextCharacters(
                        $sourceRow['FreeText'],
                        $destRow['FreeText'],
                        ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                        'Numbers for for PK' . $destRow['PrimaryKey'] . ':'
                    );
                }
            }
        } else {
            $this->assertTrue(false, 'NO data to test !');
        }
    }

    public function testTruncatedTable(): void
    {
        $query = 'SELECT COUNT(1) as nr FROM example_3';
        $stmt = self::$source->query($query);
        $sourceRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $query = 'SELECT COUNT(1) as nr FROM example_3';
        $stmt = self::$destination->query($query);
        $destRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertGreaterThan(0, $sourceRow['nr']);
        $this->assertSame(0, $destRow['nr']);
    }

    /**
     * @see BeforeScriptRunTest::testPrepareJsonSource()
     */
    public function testJson(): void
    {
        $query = 'SELECT * FROM example_1 WHERE id=' . self::SOURCE_ID_1;
        $stmt = self::$source->query($query);
        $sourceRow = $stmt->fetch(PDO::FETCH_ASSOC);
        try {
            $sourceJson = json_decode($sourceRow['json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->assertTrue(false, 'Source json cannot be decoded.');
            return;
        }

        $stmt = self::$destination->query($query);
        $anonymizedRow = $stmt->fetch(PDO::FETCH_ASSOC);

        try {
            $anonymizedJson = json_decode($anonymizedRow['json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->assertTrue(false, 'Anonymized json cannot be decoded.');
            return;
        }

        $multiByteAnonymizedWith = mb_substr(
            $anonymizedJson['key1'],
            mb_strpos($sourceJson['key1'], self::MULTI_BYTE_CHAR),
            1
        );
        $this->assertContains($multiByteAnonymizedWith, str_split(HashAnonymizer::PUNCTUATION));

        $multiByteAnonymizedWith = mb_substr(
            $anonymizedJson['key2']['key2-1'],
            mb_strpos($sourceJson['key2']['key2-1'], self::MULTI_BYTE_CHAR),
            1
        );
        $this->assertContains($multiByteAnonymizedWith, str_split(HashAnonymizer::PUNCTUATION));


        $this->assertNotEquals($sourceJson['key1'], $anonymizedJson['key1']);
        $this->assertNotEquals($sourceJson['key2']['key2-1'], $anonymizedJson['key2']['key2-1']);

        //anonymizing the same string in different places must have the same output.
        $this->assertSame($anonymizedJson['key1'], $anonymizedJson['key2']['key2-1']);

        //punctuation characters are the same and in the same position
        $this->assertFreeTextKeepPunctuation($sourceJson['key1'], $anonymizedJson['key1']);
        $this->assertFreeTextKeepPunctuation($sourceJson['key2']['key2-1'], $anonymizedJson['key2']['key2-1']);

        //lowercase characters are anonymized with other lowercase characters
        $this->assertFreeTextCharacters($sourceJson['key1'], $anonymizedJson['key1'], range('a', 'z'));
        $this->assertFreeTextCharacters($sourceJson['key2']['key2-1'], $anonymizedJson['key2']['key2-1'], range('a', 'z'));
        //uppercase characters are anonymized with other uppercase characters
        $this->assertFreeTextCharacters($sourceJson['key1'], $anonymizedJson['key1'], range('A', 'Z'));
        $this->assertFreeTextCharacters($sourceJson['key2']['key2-1'], $anonymizedJson['key2']['key2-1'], range('A', 'Z'));

        //numbers are replaced with other numbers
        $this->assertFreeTextCharacters(
            $sourceJson['key1'],
            $anonymizedJson['key1'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        );

        $this->assertFreeTextCharacters(
            $sourceJson['key2']['key2-1'],
            $anonymizedJson['key2']['key2-1'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        );

        //new lines are kept in the same position
        $this->assertFreeTextCharacters($sourceJson['key1'], $anonymizedJson['key1'], ["\n"]);
        $this->assertFreeTextCharacters($sourceJson['key2']['key2-1'], $anonymizedJson['key2']['key2-1'], ["\n"]);
        //tabs are kept in the same position
        $this->assertFreeTextCharacters($sourceJson['key1'], $anonymizedJson['key1'], ["\t"]);
        $this->assertFreeTextCharacters($sourceJson['key2']['key2-1'], $anonymizedJson['key2']['key2-1'], ["\t"]);
    }

    /**
     * @see BeforeScriptRunTest::testPrepareSerializeSource()
     */
    public function testSerialize(): void
    {
        $query = 'SELECT * FROM all_allow_null WHERE PrimaryKey=' . self::SOURCE_ID_2;
        $stmt = self::$source->query($query);
        $sourceRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $source = unserialize($sourceRow['Serialized']);

        $stmt = self::$destination->query($query);
        $anonymizedRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $anonymizedUnserialized = unserialize($anonymizedRow['Serialized']);

        $position1 = $this->strposAll($anonymizedUnserialized['key1'], "\r\n");

        $this->assertSame("\r\n", mb_substr($anonymizedUnserialized['key1'], $position1[0], 2));

        $this->assertIsArray($source);
        $this->assertIsArray($anonymizedUnserialized);

        $this->assertNotEquals($source['key1'], $anonymizedUnserialized['key1']);
        $this->assertNotEquals($source['key2']['key2-1'], $anonymizedUnserialized['key2']['key2-1']);

        $this->assertSame($anonymizedUnserialized['key1'], $anonymizedUnserialized['key1']);
        $this->assertSame($anonymizedUnserialized['key2']['key2-1'], $anonymizedUnserialized['key2']['key2-1']);

        //punctuation characters are the same and in the same position
        $this->assertFreeTextKeepPunctuation($source['key1'], $anonymizedUnserialized['key1']);
        $this->assertFreeTextKeepPunctuation($source['key2']['key2-1'], $anonymizedUnserialized['key2']['key2-1']);

        //lowercase characters are anonymized with other lowercase characters
        $this->assertFreeTextCharacters($source['key1'], $anonymizedUnserialized['key1'], range('a', 'z'));
        $this->assertFreeTextCharacters(
            $source['key2']['key2-1'],
            $anonymizedUnserialized['key2']['key2-1'],
            range('a', 'z')
        );
        //uppercase characters are anonymized with other uppercase characters
        $this->assertFreeTextCharacters($source['key1'], $anonymizedUnserialized['key1'], range('A', 'Z'));
        $this->assertFreeTextCharacters(
            $source['key2']['key2-1'],
            $anonymizedUnserialized['key2']['key2-1'],
            range('A', 'Z')
        );
        //numbers are replaced with other numbers
        $this->assertFreeTextCharacters(
            $source['key1'],
            $anonymizedUnserialized['key1'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        );
        $this->assertFreeTextCharacters(
            $source['key2']['key2-1'],
            $anonymizedUnserialized['key2']['key2-1'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        );

        //new lines are kept in the same position
        $this->assertFreeTextCharacters($source['key1'], $anonymizedUnserialized['key1'], ["\n"]);
        $this->assertFreeTextCharacters($source['key2']['key2-1'], $anonymizedUnserialized['key2']['key2-1'], ["\n"]);
        //tabs are kept in the same position
        $this->assertFreeTextCharacters($source['key1'], $anonymizedUnserialized['key1'], ["\t"]);
        $this->assertFreeTextCharacters($source['key2']['key2-1'], $anonymizedUnserialized['key2']['key2-1'], ["\t"]);
    }

    /**
     * Checks if the punctuation characters are in the same position in the anonymized string
     *
     * @param string $source
     * @param string $anonymized
     * @param string $startMsg
     */
    private function assertFreeTextKeepPunctuation($source, $anonymized, $startMsg = ''): void
    {
        $characters = str_split(HashAnonymizer::PUNCTUATION);
        $allPositions = [];
        foreach ($characters as $sign) {
            $allPositions[$sign] = $this->strposAll($source, $sign);
        }
        foreach ($allPositions as $sign => $positions) {
            foreach ($positions as $position) {
                $sourceSign = mb_substr($source, $position, 1);
                $anonymizedSign = mb_substr($anonymized, $position, 1);
                $this->assertSame(
                    $sourceSign,
                    $anonymizedSign,
                    $startMsg . "Not the same punctuation src[$sourceSign] dest[$anonymizedSign] at pos [$position]"
                );
            }
        }
    }

    private function assertFreeTextCharacters($source, $anonymized, $characters, $startMsg = ''): void
    {

        $allPositions = [];
        foreach ($characters as $sign) {
            $allPositions[$sign] = $this->strposAll($source, $sign);
        }
        foreach ($allPositions as $sign => $positions) {
            foreach ($positions as $position) {
                $anonymizedSign = mb_substr($anonymized, $position, 1);

                $this->assertContains(
                    $anonymizedSign,
                    $characters,
                    $startMsg . "Invalid sign [$anonymizedSign] not in " . implode(',', $characters) . " at pos$position in $source "
                );
            }
        }
    }


    private function strposAll($haystack, $needle): array
    {
        $offset = 0;
        $allpos = array();
        while (($pos = mb_strpos($haystack, (string)$needle, $offset)) !== false) {
            $offset = $pos + 1;
            $allpos[] = $pos;
        }
        return $allpos;
    }
}
