<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\Import\ImportData;

class AfterImport extends Event
{
    public function __construct(
        \BusinessG\BaseExcel\Data\BaseConfig $config,
        \BusinessG\BaseExcel\Driver\DriverInterface $driver,
        public ?ImportData $data = null
    ) {
        parent::__construct($config, $driver);
    }
}
