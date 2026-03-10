<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress\Storage;

use BusinessG\BaseExcel\Contract\RedisAdapterInterface;

class BridgeProgressStorage extends AbstractProgressStorage
{
    public function __construct(private RedisAdapterInterface $redis)
    {
    }

    public function get(string $key): ?string
    {
        return $this->redis->get($key);
    }

    public function set(string $key, string $value, int $ttl): void
    {
        $this->redis->setex($key, $ttl, $value);
    }

    public function lpush(string $key, string $value, int $ttl): void
    {
        $this->redis->eval(static::getLpushLuaScript(), [$key], [$value, $ttl]);
    }

    public function rpop(string $key): ?string
    {
        return $this->redis->rpop($key);
    }

    public function lrange(string $key, int $start, int $stop): array
    {
        return $this->redis->lrange($key, $start, $stop);
    }
}
