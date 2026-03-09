<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Logger;

use Psr\Log\LoggerInterface;

interface ExcelLoggerInterface
{
    public function getLogger(): LoggerInterface;
}
