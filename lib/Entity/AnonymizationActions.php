<?php

namespace PayU\MysqlDumpAnonymizer\Entity;

final class AnonymizationActions {

    public const ANONYMIZE = 1;
    public const TRUNCATE = 2;

    public const DESC = [
        'anonymize' => self::ANONYMIZE,
        'truncate' => self::TRUNCATE,
    ];



}