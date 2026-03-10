<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress\Storage;

use BusinessG\BaseExcel\Contract\RedisAdapterInterface;

/**
 * Generic ext-redis compatible adapter for RedisAdapterInterface.
 * Accepts any object with Redis-compatible API (\Redis, RedisProxy, etc.).
 * Framework packages can use this directly by injecting a Redis instance,
 * eliminating the need for framework-specific adapter classes.
 */
class PhpRedisAdapter implements RedisAdapterInterface
{
    /**
     * @param \Redis|object $redis Any object with Redis-compatible methods (get, setex, eval, rPop, lRange)
     */
    public function __construct(private object $redis)
    {
    }

    public function get(string $key): ?string
    {
        $value = $this->redis->get($key);
        return $value !== false ? (string) $value : null;
    }

    public function setex(string $key, int $ttl, string $value): void
    {
        $this->redis->setex($key, $ttl, $value);
    }

    public function eval(string $script, array $keys, array $args): mixed
    {
        return $this->redis->eval($script, array_merge($keys, $args), count($keys));
    }

    public function rpop(string $key): ?string
    {
        $value = $this->redis->rPop($key);
        return $value !== false ? (string) $value : null;
    }

    public function lrange(string $key, int $start, int $stop): array
    {
        $result = $this->redis->lRange($key, $start, $stop);
        return $result !== false ? array_map('strval', $result) : [];
    }
}
