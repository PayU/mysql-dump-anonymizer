<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class Ip implements ValueAnonymizerInterface
{
    public const BASE_16 = 16;
    public const BASE_10 = 10;

    /**
     * @var StringHashInterface
     */
    private $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }


    /**
     * SHA256            = aabbccddee ff00112233 4455667788 99aabbccdd eeff0011223 344556677 8899
     * Position          = 0123456789 0123456789 0123456789 0123456789 01234567890 123456789 0123
     * REVERSED SHA256   = 9988776655 4433221100 ffeeddccbb aa99887766 55443322110 0ffeeddcc bbaa
     *
     * Convert from hex to decimal 092486092486092486092486092486092486092486092486
     * Unique numbers 092486
     * Concatenated 4 times: 092486092486092486092486
     * First and 3rd digits are (0)-(2) and the last-4 and last-2 are (2)(8)
     * The 2 characters from hash starting at position (0) are "aa" => in decimal 170
     * The 2 characters from hash starting at position (2) are "bb" => in decimal 187
     * The 2 characters from reversed-hash starting at position (2) are "88" => in decimal 136
     * The 2 characters from reversed-hash starting at position (8) are "55" => in decimal 85
     * => Ip is 170.239.85.102
     *
     * @param \PayU\MysqlDumpAnonymizer\Entity\Value $value
     * @param array $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        $hash = $this->stringHash->sha256($value->getUnEscapedValue());
        $hashNumbers = base_convert($hash, self::BASE_16, self::BASE_10);
        $hashUniqueNumbers = implode('', array_unique(str_split($hashNumbers)));

        $hashUniqueNumbers = str_repeat($hashUniqueNumbers, 4);
        $hashUniqueNumbersLength = strlen($hashUniqueNumbers);

        return new AnonymizedValue('\''
            .base_convert(substr($hash, (int)$hashUniqueNumbers[0], 2), self::BASE_16, self::BASE_10)
            .'.'.base_convert(substr($hash, (int)$hashUniqueNumbers[2], 2), self::BASE_16, self::BASE_10)
            .'.'.base_convert(substr(strrev($hash), (int)$hashUniqueNumbers[$hashUniqueNumbersLength - 4], 2), self::BASE_16, self::BASE_10)
            .'.'.base_convert(substr(strrev($hash), (int)$hashUniqueNumbers[$hashUniqueNumbersLength - 2], 2), self::BASE_16, self::BASE_10)
            .'\'');
    }
}
