<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

interface RedisAdapterInterface
{
    public function get(string $key): ?string;

    public function setex(string $key, int $ttl, string $value): void;

    /**
     * @param string $script Lua 脚本
     * @param array $keys KEYS 参数
     * @param array $args ARGV 参数
     */
    public function eval(string $script, array $keys, array $args): mixed;

    public function rpop(string $key): ?string;

    public function lrange(string $key, int $start, int $stop): array;
}
