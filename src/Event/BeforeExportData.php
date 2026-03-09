<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\Export\ExportCallbackParam;
use BusinessG\BaseExcel\Driver\DriverInterface;

class BeforeExportData extends Event
{
    public function __construct(
        BaseConfig $config,
        DriverInterface $driver,
        public ExportCallbackParam $exportCallbackParam
    ) {
        parent::__construct($config, $driver);
    }
}
