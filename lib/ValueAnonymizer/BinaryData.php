<?php

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;

class BinaryData implements ValueAnonymizerInterface
{

    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        if ((empty($value->getUnEscapedValue())) || ($value->isExpression() === false)) {
            return new AnonymizedValue('\'\'');
        }

        $hexExpression = substr($value->getUnEscapedValue(), 2);
        $i = 0;
        $anonymizedHexExpression = '';
        do {
            $part = substr($hexExpression, $i, 64);
            $anonymizedHexExpression .= $config->getHashStringHelper()->sha256($part);
            $i += 64;

            //TODO see how big the blob can be - maybe config ?
            if ( $i >= (64*30000)) {
                break;
            }
        } while ($i < strlen($hexExpression));

        return new AnonymizedValue('0x'.$anonymizedHexExpression);
    }
}