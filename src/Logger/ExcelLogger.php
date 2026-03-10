<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Logger;

use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Contract\LoggerResolverInterface;
use Psr\Log\LoggerInterface;

class ExcelLogger implements ExcelLoggerInterface
{
    protected LoggerInterface $logger;
    protected array $config;

    public function __construct(ConfigResolverInterface $configResolver, LoggerResolverInterface $loggerResolver)
    {
        $this->config = $configResolver->get('excel.logger', ['name' => 'excel']);
        $this->logger = $loggerResolver->getLogger($this->config['name'] ?? 'excel');
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
