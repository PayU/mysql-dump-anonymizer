<?php

namespace PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig;

use PayU\MysqlDumpAnonymizer\DataType\Email;
use PayU\MysqlDumpAnonymizer\DataType\FreeText;
use PayU\MysqlDumpAnonymizer\DataType\Phone;
use PayU\MysqlDumpAnonymizer\Entity\Value;

//TODO rename with PROVIDERE-ish
final class AnonymizationColumnConfig
{
    /** @var string|boolean */
    private $dataType;
    private $eavAttributeName;
    private $eavAttributeValuesDataType;

    /** @var callable[][] */
    //private $when = [];

    public function __construct($dataType, ?string $eavAttributeName, ?array $eavAttributeValuesDataType)
    {
/*        $this->when = [
            'key=PHONE' => static function (Value $value) {
                return (new FreeText())->anonymize($value);
            },
            'key=EMAIL' => static function (Value $value) {
                return (new Email())->anonymize($value);
            },
        ];*/

/* //TODO maybe
        $this->when = [
            [
                'when'=> static function ($row) {
                    return $row['key'] === 'Phone';
                },
                'do'=> static function (Value $value) {
                    return (new Phone())->anonymize($value);
                }
            ],
            [
                'when'=> static function ($row) {
                    return $row['key'] === 'Email';
                },
                'do'=> static function (Value $value) {
                    return (new Email())->anonymize($value);
                }
            ],
        ];
*/


        $this->dataType = $dataType;
        $this->eavAttributeName = $eavAttributeName;
        $this->eavAttributeValuesDataType = $eavAttributeValuesDataType;
    }

    //TODO something like ?
    public function an0n(Value $v, $row) {
        foreach ($this->when as $when) {
            if ($when['when']($row)) {


                return $when['do']($v);
            }
        }
        throw new \RuntimeException('no way');
    }


    //dataType / row
    //

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

