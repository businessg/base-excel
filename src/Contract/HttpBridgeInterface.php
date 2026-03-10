<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

/**
 * HTTP bridge: download response creation.
 * Only needed for HTTP export scenarios; pure CLI adapters may skip this.
 */
interface HttpBridgeInterface extends ResponseFactoryInterface
{
}
