<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel;

use BusinessG\BaseExcel\Driver\DriverInterface;
use Psr\Container\ContainerInterface;

/**
 * 框架无关的 Excel Invoker 抽象基类，用于解析默认 Driver
 */
abstract class ExcelInvoker
{
    /**
     * 获取默认 driver 名称
     */
    abstract protected function getDefaultDriverName(ContainerInterface $container): string;

    /**
     * 获取 DriverFactory 类名
     */
    abstract protected function getDriverFactoryClass(): string;

    public function __invoke(ContainerInterface $container): DriverInterface
    {
        $name = $this->getDefaultDriverName($container);
        $factory = $container->get($this->getDriverFactoryClass());
        return $factory->get($name);
    }
}
