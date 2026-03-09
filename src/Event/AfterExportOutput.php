<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\Export\ExportData;
use BusinessG\BaseExcel\Driver\DriverInterface;

class AfterExportOutput extends Event
{
    public function __construct(
        BaseConfig $config,
        DriverInterface $driver,
        public ExportData $data
    ) {
        parent::__construct($config, $driver);
    }
}
