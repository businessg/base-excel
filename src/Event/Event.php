<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Driver\DriverInterface;

class Event
{
    public function __construct(
        public BaseConfig $config,
        public DriverInterface $driver
    ) {
    }
}
