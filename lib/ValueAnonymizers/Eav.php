<?php
declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ValueAnonymizers;

use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\HashAnonymizer;
use PayU\MysqlDumpAnonymizer\ValueAnonymizers\HashService\StringHashSha256;

final class Eav implements ValueAnonymizerInterface
{
    private string $attributeColumnName;

    /** @var ValueAnonymizerInterface[] */
    private array $attributeValuesAnonymizers;

    /**
     * Eav constructor.
     * @param $attributeColumnName
     * @param ValueAnonymizerInterface[] $attributeValuesAnonymizers
     */
    public function __construct(string $attributeColumnName, array $attributeValuesAnonymizers)
    {
        $this->attributeColumnName = $attributeColumnName;
        $this->attributeValuesAnonymizers = $attributeValuesAnonymizers;
    }

    /**
     * @param Value $value
     * @param Value[] $row
     * @return AnonymizedValue
     */
    public function anonymize(Value $value, array $row): AnonymizedValue
    {

        foreach ($this->attributeValuesAnonymizers as $onValue => $anonymizeLikeThis) {
            if ($row[$this->attributeColumnName]->getUnEscapedValue() === $onValue) {
                return $anonymizeLikeThis->anonymize($value, $row);
            }
        }

        return (new FreeText(new StringHashSha256(new HashAnonymizer())))->anonymize($value, $row);
    }
}
