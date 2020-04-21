<?php

declare(strict_types=1);


namespace PayU\MysqlDumpAnonymizer\Application;

use PayU\MysqlDumpAnonymizer\ConfigReader\ProviderFactory;
use PayU\MysqlDumpAnonymizer\ReadDump\LineParserFactory;
use InvalidArgumentException;

final class CommandLineParameters implements CommandLineParametersInterface
{
    private const PARAM_CONFIG_FILES = 'config';
    private const PARAM_LINE_PARSER = 'line-parser';
    private const PARAM_CONFIG_TYPE = 'config-type';
    private const PARAM_ESTIMATED_DUMP_SIZE = 'dump-size';
    private const PARAM_SHOW_PROGRESS = 'show-progress';


    private string $configFile;
    private string $lineParser;
    private string $configType;
    private int $estimatedDumpSize;
    private int $showProgress;

    private array $arguments;

    public function __construct()
    {
        $this->arguments = getopt('', [
            self::PARAM_CONFIG_FILES . '::',
            self::PARAM_LINE_PARSER . '::',
            self::PARAM_CONFIG_TYPE . '::',
            self::PARAM_ESTIMATED_DUMP_SIZE . '::',
            self::PARAM_SHOW_PROGRESS . '::'
        ]);

    }

    public function setCommandLineArguments(): void
    {
        $this->configFile = $this->arguments[self::PARAM_CONFIG_FILES] ?? '';
        $this->lineParser = $this->arguments[self::PARAM_LINE_PARSER] ?? LineParserFactory::LINE_PARSER_MYSQL_DUMP;
        $this->configType = $this->arguments[self::PARAM_CONFIG_TYPE] ?? ProviderFactory::DEFAULT_CONFIG_TYPE;
        $this->estimatedDumpSize = (int)($this->arguments[self::PARAM_ESTIMATED_DUMP_SIZE] ?? 1000000000);
        $this->showProgress = (int)($this->arguments[self::PARAM_SHOW_PROGRESS] ?? 1);
    }


    public function validate(): void
    {
        if (empty($this->configFile)) {
            throw new InvalidArgumentException('Please specify config file.');
        }
    }

    public function help(): string
    {

        return '
Usage: cat mysqldump.sql | php ' . basename($_SERVER['SCRIPT_FILENAME']) . ' --' . self::PARAM_CONFIG_FILES . '=FILENAME [OPTIONS]'
            . PHP_EOL . PHP_EOL
            . 'Options:' . PHP_EOL
            . ' --' . $this->pad(self::PARAM_CONFIG_TYPE) . ' Default Value: ' . ProviderFactory::DEFAULT_CONFIG_TYPE . PHP_EOL
            . '   ' . $this->pad('') . ' Specifies the type of the config used.' . PHP_EOL . PHP_EOL
            . ' --' . $this->pad(self::PARAM_LINE_PARSER) . ' Default Value: ' . LineParserFactory::LINE_PARSER_MYSQL_DUMP . PHP_EOL
            . '   ' . $this->pad('') . ' Specifies the type of the line parser used.' . PHP_EOL . PHP_EOL
            . ' --' . $this->pad(self::PARAM_ESTIMATED_DUMP_SIZE)
            . ' When available, specify the length of the data being anonymized.'
            . PHP_EOL
            . '   ' . $this->pad('') . ' This will be used to show progress data at runtime. ' . PHP_EOL . PHP_EOL
            . ' --' . $this->pad(self::PARAM_SHOW_PROGRESS) . ' Default value: 1' . PHP_EOL
            . '   ' . $this->pad('') . ' Set to 0 to not show progress data. ' . PHP_EOL . PHP_EOL
            . '';
    }

    private function pad($string): string
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
