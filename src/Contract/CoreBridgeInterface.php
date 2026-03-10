<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Core bridge: config + DI + event dispatcher.
 * Any framework adapter MUST implement this interface.
 */
interface CoreBridgeInterface extends ConfigResolverInterface, ObjectFactoryInterface
{
    public function getEventDispatcher(): EventDispatcherInterface;
}
