<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * 临时文件清理配置。
 *
 * 导入/导出过程中会在临时目录产生中间文件，此配置控制自动清理策略。
 * Hyperf 中通过 CleanFileProcess 进程定时执行清理；
 * Laravel 中可通过 Schedule 或手动调用清理逻辑。
 *
 *  - enabled:  是否启用自动清理
 *  - maxAge:   文件最大存活时间（秒），超过此时间未修改的临时文件将被删除
 *  - interval: 清理任务的执行间隔（秒），即多久检查一次
 *
 * 兼容旧配置键：'enable'→'enabled', 'time'→'maxAge'。
 */
final class CleanupConfig
{
    public function __construct(
        /** 是否启用自动清理 */
        public readonly bool $enabled = true,
        /** 文件最大存活时间（秒） */
        public readonly int $maxAge = 1800,
        /** 清理检查间隔（秒） */
        public readonly int $interval = 3600,
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            enabled: $raw['enabled'] ?? $raw['enable'] ?? true,
            maxAge: $raw['maxAge'] ?? $raw['time'] ?? 1800,
            interval: $raw['interval'] ?? 3600,
        );
    }
}
