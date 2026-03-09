<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseObject;
use BusinessG\BaseExcel\Driver\DriverInterface;

class ExportCallbackParam extends BaseObject
{
    public DriverInterface $driver;
    public ExportConfig $config;
    public Sheet $sheet;
    public int $page = 1;
    public int $pageSize = 10;
    public int $totalCount = 0;
}
