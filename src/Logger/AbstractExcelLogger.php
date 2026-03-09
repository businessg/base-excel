<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Logger;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Excel 日志器抽象基类，框架只需在构造函数中初始化 $logger 和 $config
 */
abstract class AbstractExcelLogger implements ExcelLoggerInterface
{
    protected LoggerInterface $logger;
    protected array $config;

    public function __construct(protected ContainerInterface $container)
    {
        $this->config = $this->resolveConfig();
        $this->logger = $this->resolveLogger();
    }

    abstract protected function resolveConfig(): array;

    abstract protected function resolveLogger(): LoggerInterface;

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
