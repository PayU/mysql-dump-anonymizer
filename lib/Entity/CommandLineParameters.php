<?php

namespace PayU\MysqlDumpAnonymizer\Entity;

use PayU\MysqlDumpAnonymizer\Services\ConfigFactory;
use PayU\MysqlDumpAnonymizer\Services\LineParserFactory;
use InvalidArgumentException;

final class CommandLineParameters
{
    private const PARAM_CONFIG_FILES = 'config';
    private const PARAM_LINE_PARSER = 'line-parser';
    private const PARAM_CONFIG_TYPE = 'config-type';

    private $configFile;
    private $lineParser = LineParserFactory::LINE_PARSER_MYSQL_DUMP;
    private $configType = ConfigFactory::DEFAULT_CONFIG_TYPE;


    public function setCommandLineArguments($args) {

        foreach ($args as $arg) {
            $value = substr($arg, strpos($arg, '=') + 1);
            if (strpos($arg, '--' . self::PARAM_CONFIG_FILES) === 0) {
                $this->configFile = $value;
            } elseif (strpos($arg, '--' . self::PARAM_LINE_PARSER) === 0) {
                $this->lineParser = $value;
            } elseif (strpos($arg, '--' . self::PARAM_CONFIG_TYPE) === 0) {
                $this->configType = $value;
            }
        }
    }


    public function validate() {

        if ($this->configFile === null) {
            throw new InvalidArgumentException('Please specify config file.');
        }

        if (ftell(STDIN) === false) {
                throw new InvalidArgumentException('No STDIN detected.');
        }

    }

    public function help() {
        return '
Usage: cat mysqldump.sql | php ' .basename($_SERVER['SCRIPT_FILENAME']).
            ' --' .self::PARAM_CONFIG_FILES. '=config1.yml,config2.yml'.
            ' [--' .self::PARAM_CONFIG_TYPE. '='.ConfigFactory::DEFAULT_CONFIG_TYPE.']'.
            ' [--' .self::PARAM_LINE_PARSER. '='.LineParserFactory::LINE_PARSER_MYSQL_DUMP.']'.
            '
            
';

    }

    /**
     * @return string
     */
    public function getConfigFile()
    {
        return $this->configFile;
    }

    /**
     * @return string
     */
    public function getLineParser(): string
    {
        return $this->lineParser;
    }

    /**
     * @return string
     */
    public function getConfigType(): string
    {
        return $this->configType;
    }







}