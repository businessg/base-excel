<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * 数据库日志配置。
 *
 * 控制是否将每次导入/导出操作记录到数据库。
 * 记录内容包括：token、类型(export/import)、配置类名、参数、进度、状态、耗时等。
 *
 *  - enabled: 是否启用数据库日志，关闭后不会写入数据库
 *  - model:   Eloquent/Hyperf Model 类名（仅在未注册 ExcelLogRepositoryInterface 时作为降级方案使用）
 *             推荐通过容器注册 ExcelLogRepositoryInterface 实现来自定义持久化逻辑
 *
 * 兼容旧配置键：'enable'→'enabled'。
 */
final class DbLogConfig
{
    public function __construct(
        /** 是否启用数据库日志 */
        public readonly bool $enabled = true,
        /** Model 类名（降级方案，优先使用 ExcelLogRepositoryInterface） */
        public readonly ?string $model = null,
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            enabled: $raw['enabled'] ?? $raw['enable'] ?? true,
            model: $raw['model'] ?? null,
        );
    }
}
