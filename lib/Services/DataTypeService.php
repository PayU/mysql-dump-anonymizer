<?php

namespace PayU\MysqlDumpAnonymizer\Services;

use PayU\MysqlDumpAnonymizer\DataType\InterfaceDataType;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationColumnConfig;
use PayU\MysqlDumpAnonymizer\Entity\DatabaseValue;
use PayU\MysqlDumpAnonymizer\Entity\DataTypes;
use PayU\MysqlDumpAnonymizer\Entity\Value;

class DataTypeService
{

    /**
     * @var DataTypes
     */
    private $dataTypes;

    public function __construct(DataTypes $dataTypes)
    {
        $this->dataTypes = $dataTypes;
    }


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
            $eavAttribute = $row[$anonymizationColumnConfig->getEavAttributeName()]->getValue()->getValue();
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

    public function anonymizeValue(Value $value, InterfaceDataType $dataType) : DatabaseValue
    {
        return $dataType->anonymize($value->getValue());

    }

    private function getDataTypeClass(string $dataType): InterfaceDataType
    {
        return $this->dataTypes->getDataTypeClass($dataType);
    }

}