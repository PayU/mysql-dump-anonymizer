<?php


namespace PayU\MysqlDumpAnonymizer\DataType;


use PayU\MysqlDumpAnonymizer\Entity\AnonymizedValue;
use PayU\MysqlDumpAnonymizer\Entity\Value;

class EavValueAnonymizer implements InterfaceDataType
{
    private $list;


    /**
     * EavValueAnonymizer constructor.
     *
     * @param array<array<column:string, value:string, valueAnonymizer:InterfaceDataType> $list
     */
    public function __construct($list)
    {

        $this->list = $list;
    }


    /**
     * @inheritDoc
     * TODO add row !
     */
    public function anonymize(Value $value): AnonymizedValue
    {

    }
}