<?php

namespace PayU\MysqlDumpAnonymizer\Services;


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
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationColumnConfig;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use RuntimeException;

class DataTypeService
{


    /**
     * @param AnonymizationColumnConfig $anonymizationColumnConfig
     * @param Value[] $row
     * @return InterfaceDataType|null
     */
    public function getDataType(AnonymizationColumnConfig $anonymizationColumnConfig, $row) : ?InterfaceDataType
    {
        if ($anonymizationColumnConfig->getDataType() === false) {
            return null;
        }

        if ($anonymizationColumnConfig->getDataType() === true) {
            $eavAttribute = $row[$anonymizationColumnConfig->getEavAttributeName()]->getValue();
            $eavValues = $anonymizationColumnConfig->getEavAttributeValuesDataType();
            if (array_key_exists($eavAttribute, $eavValues)) {
                $dataType = $eavValues[$eavAttribute];
            }else{
                //todo what happends when script finds a non-defined attribut for eav
                $dataType = 'FreeText';
            }
            return $this->getDataTypeClass($dataType);
        }

        return $this->getDataTypeClass($anonymizationColumnConfig->getDataType());

    }

    public function anonymizeValue(Value $value, InterfaceDataType $dataType) : string
    {
        return $dataType->anonymize($value->getValue());

    }

    private function getDataTypeClass(string $dataType): InterfaceDataType
    {

        switch ($dataType) {
            case 'BankData':
                return new SensitiveFreeText();
                break;
            case 'BinaryData':
                return new BinaryData();
                break;
            case 'CardData':
                return new CardData();
                break;
            case 'Credentials':
                return new Credentials();
                break;
            case 'Date':
                return new Date();
                break;
            case 'DocumentData':
                return new DocumentData();
                break;
            case 'Email':
                return new Email();
                break;
            case 'FileName':
                return new FileName();
                break;
            case 'FreeText':
                return new FreeText();
                break;
            case 'Id':
                return new Id();
                break;
            case 'Ip':
                return new Ip();
                break;
            case 'IpInt':
                return new IpInt();
                break;
            case 'Json':
                return new Json();
                break;
            case 'Phone':
                return new Phone();
                break;
            case 'SensitiveFreeText':
                return new SensitiveFreeText();
                break;
            case 'Serialized':
                return new Serialized();
                break;
            case 'Url':
                return new Url();
                break;
            case 'Username':
                return new Username();
                break;
        }

        throw new RuntimeException('Invalid data type ' . $dataType);

    }

}