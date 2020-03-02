<?php

namespace PayU\MysqlDumpAnonymizer\Provider;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizationActions;
use PayU\MysqlDumpAnonymizer\Services\ValueAnonymizerFactory;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlProviderBuilder implements InterfaceProviderBuilder
{

    public const ACTION_ANONYMIZE = 'anonymize';
    public const ACTION_TRUNCATE = 'truncate';

    public const ACTION_MAP  = [
        self::ACTION_ANONYMIZE => AnonymizationActions::ANONYMIZE,
        self::ACTION_TRUNCATE => AnonymizationActions::TRUNCATE,
    ];


    public const ACTION_KEY = 'Action';
    public const COLUMNS_KEY = 'Columns';
    public const COLUMN_NAME_KEY = 'ColumnName';
    public const DATA_TYPE_KEY = 'DataType';
    public const WHERE_KEY = 'Where';

    /** @var string */
    private $anonymizationFile;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var \PayU\MysqlDumpAnonymizer\Services\ValueAnonymizerFactory
     */
    private $valueAnonymizerFactory;

    private $onNotConfiguredTable;

    private $onNotConfiguredColumn;

    public function __construct(
        $anonymizationFile,
        Parser $parser,
        ValueAnonymizerFactory $valueAnonymizerFactory,
        int $onNotConfiguredTable,
        string $onNotConfiguredColumn
    ) {
        $this->anonymizationFile = $anonymizationFile;
        $this->parser = $parser;
        $this->valueAnonymizerFactory = $valueAnonymizerFactory;
        $this->onNotConfiguredTable = $onNotConfiguredTable;
        $this->onNotConfiguredColumn = $onNotConfiguredColumn;
    }

    public function validate(): void
    {
        if (!file_exists($this->anonymizationFile)) {
            throw new ConfigValidationException('Cannot find config file 1 ' . $this->anonymizationFile);
        }

        try {
            $anonymizationData = $this->parser->parseFile($this->anonymizationFile);
        } catch (ParseException $e) {
            throw new ConfigValidationException('Cannot parse yml format : ' . $e->getMessage());
        }

        foreach ($anonymizationData as $table => $value) {
            if (!is_array($value)) {
                throw new ConfigValidationException('Invalid config - second level must be array - [' . $table . ']');
            }

            if (!array_key_exists(self::ACTION_KEY, $value)) {
                throw new ConfigValidationException('Invalid config - Action key must be present - [' . $table . ']');
            }

            if (!array_key_exists($value[self::ACTION_KEY], self::ACTION_MAP)) {
                throw new ConfigValidationException('Invalid Action - [' . $table . ']');
            }

            if ($value[self::ACTION_KEY] !== self::ACTION_TRUNCATE && !array_key_exists(self::COLUMNS_KEY, $value)) {
                throw new ConfigValidationException('Invalid config - Columns key must be present - [' . $table . ']');
            }

            if ($value[self::ACTION_KEY] === self::ACTION_ANONYMIZE) {
                $eavColumns = [];
                $normalColumns = [];

                foreach ($value[self::COLUMNS_KEY] as $key => $columnData) {
                    if (!is_array($columnData)) {
                        throw new ConfigValidationException(
                            'Invalid config - column data not array - [' . $table . ' #' . $key . ']'
                        );
                    }

                    if (!array_key_exists(self::COLUMN_NAME_KEY, $columnData)) {
                        throw new ConfigValidationException(
                            'Invalid config - no column name key - [' . $table . ' #' . $key . ']'
                        );
                    }

                    if (!array_key_exists(self::DATA_TYPE_KEY, $columnData)) {
                        throw new ConfigValidationException(
                            'Invalid config - no data type key - [' . $table . ' ' . $columnData[self::COLUMN_NAME_KEY] . ']'
                        );
                    }

                    if ($this->valueAnonymizerFactory->valueAnonymizerExists($columnData[self::DATA_TYPE_KEY]) === false) {
                        throw new ConfigValidationException(
                            'Invalid config - invalid data type key - [' . $table . ' ' . $columnData[self::DATA_TYPE_KEY] . ']'
                        );
                    }


                    if (array_key_exists(self::WHERE_KEY, $columnData)) {
                        if (array_key_exists($columnData[self::COLUMN_NAME_KEY], $normalColumns)) {
                            throw new ConfigValidationException(
                                'Invalid config - mixed eav/normal data type [' . $table . ' ' . $columnData[self::DATA_TYPE_KEY] . ']'
                            );
                        }
                        if (strpos($columnData[self::WHERE_KEY], '=') === false) {
                            throw new ConfigValidationException(
                                'Invalid config - invalid where - [' . $table . ' ' . $columnData[self::COLUMN_NAME_KEY] . ']'
                            );
                        }
                        [$attribute, $value] = explode('=', $columnData[self::WHERE_KEY], 2);
                        $eavColumns[$columnData[self::COLUMN_NAME_KEY]][$attribute][] = $value;
                    } else {
                        if (array_key_exists($columnData[self::COLUMN_NAME_KEY], $eavColumns)) {
                            throw new ConfigValidationException(
                                'Invalid config - mixed eav/normal data type [' . $table . ' ' . $columnData[self::DATA_TYPE_KEY] . ']'
                            );
                        }
                        $normalColumns[] = $columnData[self::COLUMN_NAME_KEY];
                    }
                }

                foreach ($eavColumns as $column => $attributes) {
                    if (count($attributes) > 1) {
                        throw new ConfigValidationException(
                            'Invalid config - EAV Column multiple attributes - [' . $table . ' ' . $column . ']'
                        );
                    }
                }
            }
        }
    }

    public function buildProvider(): AnonymizationProviderInterface
    {
        $anonymizationData = $this->parser->parseFile($this->anonymizationFile);

        $tableActions = [];
        $tableColumnsData = [];

        foreach ($anonymizationData as $table => $data) {
            $tableActions[$table] = self::ACTION_MAP[$data[self::ACTION_KEY]];
            $tableColumnsData[$table] = [];

            if ($data[self::ACTION_KEY] === self::ACTION_TRUNCATE) {
                continue;
            }

            $eavColumns = [];
            foreach ($data[self::COLUMNS_KEY] as $columnData) {
                if (!array_key_exists(self::WHERE_KEY, $columnData)) {
                    $tableColumnsData[$table][$columnData[self::COLUMN_NAME_KEY]] = $this->valueAnonymizerFactory->getValueAnonymizerClass(
                        $columnData[self::DATA_TYPE_KEY],
                        []
                    );
                } else {
                    [$attribute, $value] = explode('=', $columnData[self::WHERE_KEY], 2);
                    $eavColumns[$columnData[self::COLUMN_NAME_KEY]][$attribute][$value] = $columnData[self::DATA_TYPE_KEY];
                }
            }

            foreach ($eavColumns as $columnName => $eavInfos) {
                //TODO replace below with array_key_first on php7.3
                $attribute = null;
                /** @noinspection LoopWhichDoesNotLoopInspection  */
                foreach ($eavInfos as $key => $value) {
                    $attribute = $key;
                    break;
                }
                $tableColumnsData[$table][$columnName] = $this->valueAnonymizerFactory->getValueAnonymizerClass(
                    'Eav',
                    [$attribute, $eavInfos[$attribute], $this->valueAnonymizerFactory]
                );
            }
        }

        return new AnonymizationProvider(
            $tableActions,
            $this->onNotConfiguredTable,
            $tableColumnsData,
            $this->valueAnonymizerFactory->getValueAnonymizerClass($this->onNotConfiguredColumn, [])
        );
    }
}
