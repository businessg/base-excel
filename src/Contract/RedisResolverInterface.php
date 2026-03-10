<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

interface RedisResolverInterface
{
    /**
     * @return \Redis|\Predis\Client|object
     */
    public function getRedis(string $connection = 'default'): object;
}
