<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\Import\ImportRowCallbackParam;
use BusinessG\BaseExcel\Driver\DriverInterface;

class BeforeImportData extends Event
{
    public function __construct(
        BaseConfig $config,
        DriverInterface $driver,
        public ImportRowCallbackParam $importCallbackParam
    ) {
        parent::__construct($config, $driver);
    }
}
