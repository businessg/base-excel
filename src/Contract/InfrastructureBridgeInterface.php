<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

/**
 * Infrastructure bridge: logging + deferred execution.
 */
interface InfrastructureBridgeInterface extends LoggerResolverInterface, DeferInterface
{
}
