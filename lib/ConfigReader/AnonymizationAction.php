<?php

declare(strict_types=1);

namespace PayU\MysqlDumpAnonymizer\ConfigReader;

use MyCLabs\Enum\Enum;

/**
 * @method static AnonymizationAction ANONYMIZE()
 * @method static AnonymizationAction TRUNCATE()
 */
final class AnonymizationAction extends Enum
{
    public const ANONYMIZE = 1;
    public const TRUNCATE = 2;
}
