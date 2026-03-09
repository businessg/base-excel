<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Queue;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Event\Error;
use BusinessG\BaseExcel\ExcelInterface;
use Psr\Container\ContainerInterface;

/**
 * Job 公共逻辑 Trait，Laravel/Hyperf BaseJob 复用
 */
trait ExcelJobTrait
{
    public BaseConfig $config;

    protected function getExcel(): ExcelInterface
    {
        return $this->getContainer()->get(ExcelInterface::class);
    }

    /**
     * 派发 Error 事件，供 failed/fail 回调使用
     */
    protected function dispatchError(\Throwable $e): void
    {
        $excel = $this->getExcel();
        $driver = $excel->getDriver($this->config->getDriverName());
        $excel->getEvent()->dispatch(new Error($this->config, $driver, $e));
    }

    /**
     * 获取容器，由框架 BaseJob 实现
     */
    abstract protected function getContainer(): ContainerInterface;
}
