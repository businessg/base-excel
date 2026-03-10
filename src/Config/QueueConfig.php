<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * 异步队列配置。
 *
 * 当导入/导出任务设置为异步 (isAsync=true) 时，任务会被推送到队列执行。
 *  - connection: 队列连接名称
 *    - Laravel: config/queue.php 中的 connections key（如 redis、database）
 *    - Hyperf:  config/autoload/async_queue.php 中的 key（如 default）
 *  - channel: 队列通道/管道名称，用于区分不同优先级或类型的任务
 *    - Laravel: 对应 Queue::onQueue($channel)
 *    - Hyperf:  Hyperf AsyncQueue 暂不区分 channel，设为 'default' 即可
 *
 * 兼容旧配置键 'name'（已废弃，请使用 'connection'）。
 */
final class QueueConfig
{
    public function __construct(
        /** 队列连接名称 */
        public readonly string $connection = 'default',
        /** 队列通道名称 */
        public readonly string $channel = 'default',
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            connection: $raw['connection'] ?? $raw['name'] ?? 'default',
            channel: $raw['channel'] ?? 'default',
        );
    }
}
