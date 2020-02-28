<?php

namespace PayU\MysqlDumpAnonymizer\Services;

use PayU\MysqlDumpAnonymizer\ValueAnonymizer\InterfaceDataType;
use PayU\MysqlDumpAnonymizer\Provider\ColumnAnonymizationProvider;
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
     * @param \PayU\MysqlDumpAnonymizer\Provider\ColumnAnonymizationProvider $anonymizationColumnConfig
     * @param Value[] $row Associative array columnName => Value Object
     * @return InterfaceDataType|null
     */
    public function getDataType(ColumnAnonymizationProvider $anonymizationColumnConfig, $row) : ?string
    {
        if ($anonymizationColumnConfig->getDataType() === false) {
            return null;
        }

        if ($anonymizationColumnConfig->getDataType() === true) {
            $eavAttribute = $row[$anonymizationColumnConfig->getEavAttributeName()]->getUnEscapedValue();
            $eavValues = $anonymizationColumnConfig->getEavAttributeValuesDataType();
            if (array_key_exists($eavAttribute, $eavValues)) {
                $dataType = $eavValues[$eavAttribute];
            }else{
                //todo what happens when script finds a non-defined attribute for eav ?
                $dataType = 'FreeText';
            }
            return $dataType;
        }

        return $anonymizationColumnConfig->getDataType();

    }

    public function getDataTypeClass(string $dataType): InterfaceDataType
    {
        return $this->dataTypes->getDataTypeClass($dataType);
    }

}