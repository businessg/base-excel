<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

/**
 * Full-featured bridge aggregating all sub-bridge interfaces.
 * Kept for backward compatibility -- framework adapters that implement this
 * automatically satisfy CoreBridge, StorageBridge, HttpBridge, and InfrastructureBridge.
 */
interface FrameworkBridgeInterface extends
    CoreBridgeInterface,
    StorageBridgeInterface,
    HttpBridgeInterface,
    InfrastructureBridgeInterface
{
}
