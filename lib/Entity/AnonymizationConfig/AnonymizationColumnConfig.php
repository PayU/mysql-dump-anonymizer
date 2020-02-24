<?php

namespace PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig;

final class AnonymizationColumnConfig
{
    /** @var string|boolean */
    private $dataType;
    private $eavAttributeName;
    private $eavAttributeValuesDataType;

    public function __construct($dataType, ?string $eavAttributeName, ?array $eavAttributeValuesDataType)
    {
        $this->dataType = $dataType;
        $this->eavAttributeName = $eavAttributeName;
        $this->eavAttributeValuesDataType = $eavAttributeValuesDataType;
    }

    /**
     * @return string|boolean
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return string|null
     */
    public function getEavAttributeName(): ?string
    {
        return $this->eavAttributeName;
    }

    /**
     * @return array|null
     */
    public function getEavAttributeValuesDataType(): ?array
    {
        return $this->eavAttributeValuesDataType;
    }



}

