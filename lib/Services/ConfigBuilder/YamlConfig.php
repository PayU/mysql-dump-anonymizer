<?php

namespace PayU\MysqlDumpAnonymizer\Services\ConfigBuilder;

use PayU\MysqlDumpAnonymizer\Entity\AnonymizationActions;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationColumnConfig;
use PayU\MysqlDumpAnonymizer\Entity\AnonymizationConfig\AnonymizationConfig;
use PayU\MysqlDumpAnonymizer\Entity\DataTypes;
use PayU\MysqlDumpAnonymizer\Exceptions\ConfigValidationException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlConfig implements InterfaceConfigBuilder
{

    public const ACTION_ANONYMIZE = AnonymizationActions::ANONYMIZE;
    public const ACTION_TRUNCATE = AnonymizationActions::TRUNCATE;
    public const ACTION_KEY = 'Action';
    public const COLUMNS_KEY = 'Columns';
    public const COLUMN_NAME_KEY = 'ColumnName';
    public const DATA_TYPE_KEY = 'DataType';
    public const WHERE_KEY = 'Where';

    /** @var string */
    private $anonymizationFile;

    /** @var string */
    private $noAnonymizationFile;
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var DataTypes
     */
    private $dataTypes;

    public function __construct($anonymizationFile, $noAnonymizationFile, Parser $parser, DataTypes $dataTypes)
    {
        $this->anonymizationFile = $anonymizationFile;
        $this->noAnonymizationFile = $noAnonymizationFile;
        $this->parser = $parser;
        $this->dataTypes = $dataTypes;
    }

    public function validate(): void
    {
        if (!file_exists($this->anonymizationFile)) {
            throw new ConfigValidationException('Cannot find config file 1 ' . $this->anonymizationFile);
        }
        if (!file_exists($this->noAnonymizationFile)) {
            throw new ConfigValidationException('Cannot find config file 2 ' . $this->noAnonymizationFile);
        }

        try {
            $anonymizationData = $this->parser->parseFile($this->anonymizationFile);
            $noAnonymizationData = $this->parser->parseFile($this->noAnonymizationFile);
        } catch (ParseException $e) {
            throw new ConfigValidationException('Cannot parse yml format : ' . $e->getMessage());
        }

        foreach ($noAnonymizationData as $table => $columns) {
            if (!is_array($columns)) {
                throw new ConfigValidationException('No anonymization file table ' . $table . ' doesnt contain any columns');
            }
        }

        foreach ($anonymizationData as $table => $value) {

            if (!is_array($value)) {
                throw new ConfigValidationException('Invalid config - second level must be array - [' . $table . ']');
            }

            if (!array_key_exists(self::ACTION_KEY, $value)) {
                throw new ConfigValidationException('Invalid config - Action key must be present - [' . $table . ']');
            }

            if (!in_array($value[self::ACTION_KEY], [self::ACTION_TRUNCATE, self::ACTION_ANONYMIZE], true)) {
                throw new ConfigValidationException('Invalid Action - [' . $table . ']');
            }

            if ($value[self::ACTION_KEY] !== self::ACTION_TRUNCATE && !array_key_exists(self::COLUMNS_KEY, $value)) {
                throw new ConfigValidationException('Invalid config - Columns key must be present - [' . $table . ']');
            }

            if ($value[self::ACTION_KEY] === self::ACTION_ANONYMIZE) {

                $eavColumns = [];

                foreach ($value[self::COLUMNS_KEY] as $key => $columnData) {

                    if (!is_array($columnData)) {
                        throw new ConfigValidationException('Invalid config - column data not array - [' . $table . ' #' . $key . ']');
                    }

                    if (!array_key_exists(self::COLUMN_NAME_KEY, $columnData)) {
                        throw new ConfigValidationException('Invalid config - no column name key - [' . $table . ' #' . $key . ']');
                    }

                    if (!array_key_exists(self::DATA_TYPE_KEY, $columnData)) {
                        throw new ConfigValidationException('Invalid config - no data type key - [' . $table . ' ' . $columnData[self::COLUMN_NAME_KEY] . ']');
                    }

                    if ($this->dataTypes->dataTypeExists($columnData[self::DATA_TYPE_KEY]) === false) {
                        throw new ConfigValidationException('Invalid config - invalid data type key - [' . $table . ' ' . $columnData[self::DATA_TYPE_KEY] . ']');
                    }


                    if (array_key_exists(self::WHERE_KEY, $columnData)) {
                        if (strpos($columnData[self::WHERE_KEY], '=') === false) {
                            throw new ConfigValidationException('Invalid config - invalid where - [' . $table . ' ' . $columnData[self::COLUMN_NAME_KEY] . ']');
                        }
                        [$attribute, $value] = explode('=', $columnData[self::WHERE_KEY], 2);
                        $eavColumns[$columnData[self::COLUMN_NAME_KEY]][$attribute][] = $value;
                    }
                }

                foreach ($eavColumns as $column => $attributes) {
                    if (count($attributes) > 1) {
                        throw new ConfigValidationException('Invalid config - EAV Column multiple attributes - [' . $table . ' ' . $column . ']');
                    }
                }
            }
        }
    }


    public function buildConfig(): AnonymizationConfig
    {
        $anonymizationData = $this->parser->parseFile($this->anonymizationFile);
        $noAnonymizationData = $this->parser->parseFile($this->noAnonymizationFile);

        $anonymizationConfig = new AnonymizationConfig();

        foreach ($noAnonymizationData as $table => $columns) {
            $anonymizationConfig->addConfig($table, self::ACTION_ANONYMIZE);
            $actionConfig = $anonymizationConfig->getActionConfig($table);
            foreach (array_keys($columns) as $column) {
                $actionConfig->addColumn($column, new AnonymizationColumnConfig(false, null,null));
            }
        }


        foreach ($anonymizationData as $table => $data) {

            if ($data[self::ACTION_KEY] === self::ACTION_TRUNCATE) {
                $anonymizationConfig->addConfig($table, self::ACTION_TRUNCATE);
            }

            if ($data[self::ACTION_KEY] === self::ACTION_ANONYMIZE) {

                $anonymizationConfig->addConfig($table, self::ACTION_ANONYMIZE);
                $actionConfig = $anonymizationConfig->getActionConfig($table);

                $eavColumns = [];
                foreach ($data[self::COLUMNS_KEY] as $columnData) {

                    if (!array_key_exists(self::WHERE_KEY, $columnData)) {
                        $actionConfig->addColumn($columnData[self::COLUMN_NAME_KEY], new AnonymizationColumnConfig($columnData[self::DATA_TYPE_KEY], null, null));
                    } else {
                        [$attribute, $value] = explode('=', $columnData[self::WHERE_KEY], 2);
                        $eavColumns[$columnData[self::COLUMN_NAME_KEY]][$attribute][$value] = $columnData[self::DATA_TYPE_KEY];
                    }
                }

                foreach ($eavColumns as $columnName => $eavInfo) {
                    foreach ($eavInfo as $eavAttribute => $eavValues) {
                        $actionConfig->addColumn($columnName, new AnonymizationColumnConfig(true, $eavAttribute, $eavValues));
                    }
                }

            }
        }

        return $anonymizationConfig;


    }

}