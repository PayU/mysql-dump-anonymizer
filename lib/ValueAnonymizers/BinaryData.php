<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

final class BinaryData implements ValueAnonymizerInterface
{
    /**
     * @var StringHashInterface
     */
    private $stringHash;

    public function __construct(StringHashInterface $stringHash)
    {
        $this->stringHash = $stringHash;
    }

    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        if ((empty($value->getUnEscapedValue())) || ($value->isExpression() === false)) {
            return new AnonymizedValue('\'\'');
        }

        $hexExpression = substr($value->getUnEscapedValue(), 2);
        $i = 0;
        $anonymizedHexExpression = '';
        do {
            $part = substr($hexExpression, $i, 64);
            $anonymizedHexExpression .= $this->stringHash->sha256($part);
            $i += 64;

            //TODO see how big the blob can be - maybe config ?
            if ($i >= (64*30000)) {
                break;
            }
        } while ($i < strlen($hexExpression));

        return new AnonymizedValue('0x'.$anonymizedHexExpression);
    }
}
