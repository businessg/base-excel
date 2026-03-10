<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

interface ConfigResolverInterface
{
    public function get(string $key, mixed $default = null): mixed;
}
