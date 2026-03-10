<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

interface ObjectFactoryInterface
{
    public function make(string $class, array $params = []): object;
}
