<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

use Psr\Log\LoggerInterface;

interface LoggerResolverInterface
{
    public function getLogger(string $channel = 'default'): LoggerInterface;
}
