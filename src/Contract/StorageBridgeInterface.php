<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

/**
 * Storage bridge: Redis + Filesystem.
 * Required for async operations and progress tracking.
 */
interface StorageBridgeInterface extends RedisResolverInterface, FilesystemResolverInterface
{
}
