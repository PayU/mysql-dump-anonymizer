<?php


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Services\DataTypeFactory;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\Config;

class Eav implements ValueAnonymizerInterface
{
    private $attributeColumnName;
    private $attributeValues;
    /**
     * @var DataTypeFactory
     */
    private $dataTypes;

    /**
     * EavValueAnonymizer constructor.
     * @param $attributeColumnName
     * @param array $attributeValues
     * @param DataTypeFactory $dataTypes
     */
    public function __construct($attributeColumnName, array $attributeValues, DataTypeFactory $dataTypes)
    {
        $this->attributeColumnName = $attributeColumnName;
        $this->attributeValues = $attributeValues;
        $this->dataTypes = $dataTypes;
    }


    /**
     * @param Value $value
     * @param Value[] $row
     * @param Config $config
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row, Config $config): AnonymizedValue
    {
        foreach ($this->attributeValues as $onValue => $anonymizeLikeThis) {
            if ($row[$this->attributeColumnName]->getUnEscapedValue() === $onValue) {
                return $this->dataTypes->getDataTypeClass($anonymizeLikeThis, [])->anonymize($value, $row, $config);
            }
        }

        return $this->dataTypes->getDataTypeClass('FreeText', [])->anonymize($value, $row, $config);
    }
}
