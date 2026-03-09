<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\Export\Sheet;
use BusinessG\BaseExcel\Driver\DriverInterface;

class BeforeExportSheet extends Event
{
    public function __construct(
        BaseConfig $config,
        DriverInterface $driver,
        public Sheet $sheet
    ) {
        parent::__construct($config, $driver);
    }
}
