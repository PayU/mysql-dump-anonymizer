<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer;

use PayU\MysqlDumpAnonymizer\ConfigReader\ProviderFactory;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserFactory;
use InvalidArgumentException;

final class CommandLineParameters
{
    private const PARAM_CONFIG_FILES = 'config';
    private const PARAM_LINE_PARSER = 'line-parser';
    private const PARAM_CONFIG_TYPE = 'config-type';
    private const PARAM_ESTIMATED_DUMP_SIZE = 'dump-size';
    private const PARAM_SHOW_PROGRESS = 'show-progress';


    private $configFile;
    private $lineParser = LineParserFactory::LINE_PARSER_MYSQL_DUMP;
    private $configType = ProviderFactory::DEFAULT_CONFIG_TYPE;
    private $estimatedDumpSize = 1370000000;
    private $showProgress = 1;


    public function setCommandLineArguments($args): void
    {

        foreach ($args as $arg) {
            $value = substr($arg, strpos($arg, '=') + 1);
            if (strpos($arg, '--' . self::PARAM_CONFIG_FILES) === 0) {
                $this->configFile = $value;
            } elseif (strpos($arg, '--' . self::PARAM_LINE_PARSER) === 0) {
                $this->lineParser = $value;
            } elseif (strpos($arg, '--' . self::PARAM_CONFIG_TYPE) === 0) {
                $this->configType = $value;
            } elseif (strpos($arg, '--' . self::PARAM_ESTIMATED_DUMP_SIZE) === 0) {
                $this->estimatedDumpSize = (int)$value;
            } elseif (strpos($arg, '--' . self::PARAM_SHOW_PROGRESS) === 0) {
                $this->showProgress = (bool)$value;
            }
        }
    }


    public function validate(): void
    {

        if ($this->configFile === null) {
            throw new InvalidArgumentException('Please specify config file.');
        }

        if (ftell(STDIN) === false) {
            throw new InvalidArgumentException('No STDIN detected.');
        }
    }

    public static function help(): string
    {

        return '
Usage: cat mysqldump.sql | php ' .basename($_SERVER['SCRIPT_FILENAME']).' --' .self::PARAM_CONFIG_FILES. '=FILENAME [OPTIONS]'
            .PHP_EOL.PHP_EOL
            .'Options:'.PHP_EOL
            .' --' .self::pad(self::PARAM_CONFIG_TYPE). ' Default Value: '.ProviderFactory::DEFAULT_CONFIG_TYPE.PHP_EOL
            .'   ' .self::pad('').' Specifies the type of the config used.'.PHP_EOL.PHP_EOL
            .' --' .self::pad(self::PARAM_LINE_PARSER). ' Default Value: '.LineParserFactory::LINE_PARSER_MYSQL_DUMP.PHP_EOL
            .'   ' .self::pad('').' Specifies the type of the line parser used.'.PHP_EOL.PHP_EOL
            .' --' .self::pad(self::PARAM_ESTIMATED_DUMP_SIZE)
            .' When available, specify the length of the data being anonymized.'
            .PHP_EOL
            .'   ' .self::pad('').' This will be used to show progress data at runtime. '.PHP_EOL.PHP_EOL
            .' --' .self::pad(self::PARAM_SHOW_PROGRESS).' Default value: 1'.PHP_EOL
            .'   ' .self::pad('').' Set to 0 to not show progress data. '.PHP_EOL.PHP_EOL
            .'';
    }

    private static function pad($string) : string
    {
        return str_pad($string, 24, ' ', STR_PAD_RIGHT);
    }

    /**
     * @return string
     */
    public function getConfigType(): string
    {
        return $this->configType;
    }

    /**
     * @return string
     */
    public function getConfigFile(): string
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
     * @return int
     */
    public function getEstimatedDumpSize(): int
    {
        return $this->estimatedDumpSize;
    }

    /**
     * @return bool
     */
    public function isShowProgress(): bool
    {
        return (bool)$this->showProgress;
    }

}
