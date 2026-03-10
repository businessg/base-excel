<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * 进度追踪配置。
 *
 * 用于控制导入/导出任务的实时进度存储（基于 Redis）。
 * 前端可通过 token 轮询接口获取当前进度百分比、状态等信息。
 *
 *  - enabled:    是否启用进度追踪，关闭后不会写入 Redis
 *  - prefix:     Redis key 前缀，用于隔离不同应用的进度数据，最终 key 格式: {prefix}:{token}
 *  - ttl:        进度数据在 Redis 中的过期时间（秒），过期后自动清除
 *  - connection: Redis 连接名称
 *    - Laravel: config/database.php redis.connections 中的 key
 *    - Hyperf:  config/autoload/redis.php 中的 pool name
 *
 * 兼容旧配置键：'enable'→'enabled', 'expire'→'ttl', 'redis.connection'/'redis.pool'→'connection'。
 */
final class ProgressConfig
{
    public function __construct(
        /** 是否启用进度追踪 */
        public readonly bool $enabled = true,
        /** Redis key 前缀 */
        public readonly string $prefix = 'Excel',
        /** 进度数据过期时间（秒） */
        public readonly int $ttl = 3600,
        /** Redis 连接名称 */
        public readonly string $connection = 'default',
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            enabled: $raw['enabled'] ?? $raw['enable'] ?? true,
            prefix: $raw['prefix'] ?? 'Excel',
            ttl: $raw['ttl'] ?? $raw['expire'] ?? 3600,
            connection: $raw['connection'] ?? $raw['redis']['connection'] ?? $raw['redis']['pool'] ?? 'default',
        );
    }
}
