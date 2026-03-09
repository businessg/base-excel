<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Queue;

use BusinessG\BaseExcel\Data\BaseConfig;

interface ExcelQueueInterface
{
    public function push(BaseConfig $config): void;
}
