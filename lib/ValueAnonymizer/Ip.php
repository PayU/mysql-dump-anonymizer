<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;

class Ip implements ValueAnonymizerInterface
{

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
     * @param Value $value
     * @param array $row
     * @param Config $config
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        $hash = $config->getHashStringHelper()->sha256($value->getUnEscapedValue());
        $qweqwe = base_convert($hash, 16, 10);
        $hashUniqueNumbers = implode('', array_unique(str_split($qweqwe)));

        $hashUniqueNumbers = str_repeat($hashUniqueNumbers, 4);
        $hashUniqueNumbersLength = strlen($hashUniqueNumbers);

        return new AnonymizedValue('\''
            .base_convert(substr($hash, $hashUniqueNumbers[0], 2), 16, 10)
            .'.'.base_convert(substr($hash, $hashUniqueNumbers[2], 2), 16, 10)
            .'.'.base_convert(substr(strrev($hash), $hashUniqueNumbers[$hashUniqueNumbersLength-4], 2), 16, 10)
            .'.'.base_convert(substr(strrev($hash), $hashUniqueNumbers[$hashUniqueNumbersLength-2], 2), 16, 10)
            .'\'');
    }
}
