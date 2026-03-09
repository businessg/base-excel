<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress\Storage;

use Redis;

/**
 * 基于 ext-redis (php-redis) 的进度存储实现
 */
class PhpRedisProgressStorage extends AbstractProgressStorage
{
    protected Redis $redis;

    public function __construct(array $config = [])
    {
        $this->redis = new Redis();
        $host = $config['host'] ?? '127.0.0.1';
        $port = (int) ($config['port'] ?? 6379);
        $auth = $config['auth'] ?? null;
        $db = (int) ($config['db'] ?? 0);

        $this->redis->connect($host, $port);
        if ($auth !== null && $auth !== '') {
            $this->redis->auth($auth);
        }
        if ($db > 0) {
            $this->redis->select($db);
        }
    }

    public function get(string $key): ?string
    {
        $value = $this->redis->get($key);
        return $value !== false ? $value : null;
    }

    public function set(string $key, string $value, int $ttl): void
    {
        $this->redis->setex($key, $ttl, $value);
    }

    public function lpush(string $key, string $value, int $ttl): void
    {
        $this->redis->eval(static::getLpushLuaScript(), [$key, $value, $ttl], 1);
    }

    public function rpop(string $key): ?string
    {
        $value = $this->redis->rPop($key);
        return $value !== false ? $value : null;
    }

    public function lrange(string $key, int $start, int $stop): array
    {
        $result = $this->redis->lRange($key, $start, $stop);
        return $result !== false ? array_map('strval', $result) : [];
    }
}
