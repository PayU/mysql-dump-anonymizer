<?php
declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ConfigReader\ValueAnonymizers\ValueAnonymizerFactory;
use PayU\MysqlDumpAnonymizer\ReadDump\Value;

final class Eav implements ValueAnonymizerInterface
{
    private $attributeColumnName;
    private $attributeValues;
    /**
     * @var ValueAnonymizerFactory
     */
    private $valueAnonymizerFactory;

    /**
     * Eav constructor.
     * @param $attributeColumnName
     * @param array $attributeValues
     * @param ValueAnonymizerFactory $valueAnonymizerFactory
     */
    public function __construct($attributeColumnName, array $attributeValues, ValueAnonymizerFactory $valueAnonymizerFactory)
    {
        $this->attributeColumnName = $attributeColumnName;
        $this->attributeValues = $attributeValues;
        $this->valueAnonymizerFactory = $valueAnonymizerFactory;

    }


    /**
     * @param \PayU\MysqlDumpAnonymizer\ReadDump\Value $value
     * @param \PayU\MysqlDumpAnonymizer\ReadDump\Value[] $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue
    {
        foreach ($this->attributeValues as $onValue => $anonymizeLikeThis) {
            if ($row[$this->attributeColumnName]->getUnEscapedValue() === $onValue) {
                return $this->valueAnonymizerFactory->getValueAnonymizerClass($anonymizeLikeThis, [])->anonymize($value, $row);
            }
        }

        return $this->valueAnonymizerFactory->getValueAnonymizerClass('FreeText', [])->anonymize($value, $row);
    }
}
