<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\Export\ExportData;

class AfterExport extends Event
{
    public function __construct(
        \BusinessG\BaseExcel\Data\BaseConfig $config,
        \BusinessG\BaseExcel\Driver\DriverInterface $driver,
        public ?ExportData $data = null
    ) {
        parent::__construct($config, $driver);
    }
}
