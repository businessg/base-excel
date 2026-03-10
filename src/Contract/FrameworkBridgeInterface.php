<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

use Psr\EventDispatcher\EventDispatcherInterface;

interface FrameworkBridgeInterface extends
    ConfigResolverInterface,
    ObjectFactoryInterface,
    RedisResolverInterface,
    LoggerResolverInterface,
    ResponseFactoryInterface,
    FilesystemResolverInterface,
    DeferInterface
{
    public function getEventDispatcher(): EventDispatcherInterface;
}
