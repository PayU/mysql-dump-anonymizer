<?php
declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\ValueAnonymizer;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Services\ValueAnonymizerFactory;
use PayU\MysqlDumpAnonymizer\Entity\Value;

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
     * @param Value $value
     * @param Value[] $row
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
