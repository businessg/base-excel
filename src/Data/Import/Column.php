<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Import;

use BusinessG\BaseExcel\Data\BaseObject;

class Column extends BaseObject
{
    public const TYPE_STRING = 0x01;
    public const TYPE_INT = 0x02;
    public const TYPE_DOUBLE = 0x04;
    public const TYPE_TIMESTAMP = 0x08;

    public string $title = '';
    public int $type = self::TYPE_STRING;
    public string $field = '';
}
