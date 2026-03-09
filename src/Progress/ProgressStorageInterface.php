<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

/**
 * 进度存储接口，约束 Redis 相关操作，由框架或默认实现提供
 */
interface ProgressStorageInterface
{
    public function get(string $key): ?string;

    public function set(string $key, string $value, int $ttl): void;

    public function lpush(string $key, string $value, int $ttl): void;

    public function rpop(string $key): ?string;
}
