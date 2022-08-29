<?php


namespace PayU\MysqlDumpAnonymizer\Exceptions;

use InvalidArgumentException;
use PayU\MysqlDumpAnonymizer\AnonymizationProvider\ValueAnonymizerInterface;
use Throwable;

class FallbackException extends InvalidArgumentException
{
    private ValueAnonymizerInterface $valueAnonymizer;

    public function __construct(ValueAnonymizerInterface $valueAnonymizer, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->valueAnonymizer = $valueAnonymizer;
    }

    public function getValueAnonymizer(): ValueAnonymizerInterface
    {
        return $this->valueAnonymizer;
    }

}
