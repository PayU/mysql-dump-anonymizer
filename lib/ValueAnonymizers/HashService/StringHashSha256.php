<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService;

final class StringHashSha256 implements StringHashInterface
{

    private static string $salt;

    private HashAnonymizerInterface $hashAnonymizer;


    public function __construct(HashAnonymizerInterface $hashAnonymizer)
    {
        $this->hashAnonymizer = $hashAnonymizer;
    }

    public static function setSalt(string $salt): void
    {
        self::$salt = $salt;
    }

    /**
     * This will create the same hash for a given string
     * ABC|123 => JED|493  (with  $anonymizePunctuation = false)
     * ABC|123 => JED#493  (with  $anonymizePunctuation = false)
     *
     * @param string $string
     * @param bool $anonymizePunctuation
     * @return string
     */
    public function hashKeepFormat($string, $anonymizePunctuation = false): string
    {
        $string = (string)$string;

        $this->hashAnonymizer->initializeHashString(
            $this->sha256($string . strrev($string)) . $this->sha256(strrev($string) . $string)
        );

        $returnString = '';

        $hasMultiBytesChars = false;
        if (preg_match("/[^a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"'\\\[\]]/", $string)) {
            $hasMultiBytesChars = true;
        }

        $stringLength = $hasMultiBytesChars ? mb_strlen($string) : strlen($string);

        for ($i = 0; $i < $stringLength; $i++) {
            $char = $string[$i];
            if ($hasMultiBytesChars) {
                $char = mb_substr($string, $i, 1);
            }

            if (ctype_digit($char)) {
                //if 0-9 replace with a number
                $returnString .= $this->hashAnonymizer->getNextNumber();
            } elseif (ctype_upper($char)) {
                //if A-Z replace with an uppercase letter
                $returnString .= strtoupper($this->hashAnonymizer->getNextLetter());
            } elseif (ctype_lower($char)) {
                //if a-a replace with an lowercase letter
                $returnString .= strtolower($this->hashAnonymizer->getNextLetter());
            } elseif ($char === ' ') {
                //keep spaces
                $returnString .= ' ';
            } elseif (in_array($char, ["\n", "\r", "\t"])) {
                //dont anonymize new lines and tabs
                $returnString .= $char;
            } elseif ($this->hashAnonymizer->isPunctuation($char)) {
                if ($anonymizePunctuation) {
                    $returnString .= $this->hashAnonymizer->getNextPunctuation();
                } else {
                    $returnString .= $char;
                }
            } else {
                //non printable characters
                $returnString .= $this->hashAnonymizer->getNextPunctuation();
            }
        }

        return $returnString;
    }

    public function sha256($string, $rawOutput = false): string
    {
        return hash('sha256', $string . self::$salt, $rawOutput);
    }

    public function hashIpAddressString(string $string): string
    {
        $this->hashAnonymizer->initializeHashString(
            $this->sha256($string . strrev($string) . $this->sha256(strrev($string) . $string))
        );

        return $this->hashAnonymizer->getNextNumberBetween0And255() . '.'
            . $this->hashAnonymizer->getNextNumberBetween0And255() . '.'
            . $this->hashAnonymizer->getNextNumberBetween0And255() . '.'
            . $this->hashAnonymizer->getNextNumberBetween0And255();
    }

}
