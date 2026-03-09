<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Event;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Driver\DriverInterface;

class Error extends Event
{
    public function __construct(
        BaseConfig $config,
        DriverInterface $driver,
        public \Throwable $exception
    ) {
        parent::__construct($config, $driver);
    }
}
