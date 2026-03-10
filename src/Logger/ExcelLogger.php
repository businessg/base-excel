<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Logger;

use BusinessG\BaseExcel\Config\ExcelConfig;
use BusinessG\BaseExcel\Config\LoggingConfig;
use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Contract\LoggerResolverInterface;
use Psr\Log\LoggerInterface;

class ExcelLogger implements ExcelLoggerInterface
{
    protected LoggerInterface $logger;
    protected LoggingConfig $loggingConfig;

    public function __construct(ConfigResolverInterface $configResolver, LoggerResolverInterface $loggerResolver)
    {
        $this->loggingConfig = ExcelConfig::fromArray($configResolver->get('excel', []))->logging;
        $this->logger = $loggerResolver->getLogger($this->loggingConfig->channel);
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getConfig(): array
    {
        return ['channel' => $this->loggingConfig->channel];
    }
}
