<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Import;

use BusinessG\BaseExcel\Data\BaseObject;
use BusinessG\BaseExcel\Driver\DriverInterface;

class ImportRowCallbackParam extends BaseObject
{
    public DriverInterface $driver;
    public ImportConfig $config;
    public Sheet $sheet;
    public array $row = [];
    public int $rowIndex = 0;
}
