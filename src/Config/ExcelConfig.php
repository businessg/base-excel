<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * Excel 组件的顶层配置 DTO。
 *
 * 集中管理所有子模块配置，并兼容新旧两种配置格式。
 * 通常通过 ExcelConfig::fromArray(config('excel', [])) 构建。
 */
final class ExcelConfig
{
    public function __construct(
        /** 默认驱动名称，对应 drivers 数组中的 key，如 'xlswriter' */
        public readonly string $default = 'xlswriter',
        /** 驱动列表，key 为驱动名，value 为驱动配置数组 */
        public readonly array $drivers = [],
        /** 日志配置 */
        public readonly LoggingConfig $logging = new LoggingConfig(),
        /** 异步队列配置 */
        public readonly QueueConfig $queue = new QueueConfig(),
        /** 进度追踪配置 */
        public readonly ProgressConfig $progress = new ProgressConfig(),
        /** 数据库日志配置 */
        public readonly DbLogConfig $dbLog = new DbLogConfig(),
        /** 临时文件清理配置 */
        public readonly CleanupConfig $cleanup = new CleanupConfig(),
    ) {
    }

    /**
     * Parse from raw config array (supports both old and new config formats).
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            default: $raw['default'] ?? 'xlswriter',
            drivers: self::resolveDrivers($raw),
            logging: LoggingConfig::fromArray($raw['logging'] ?? $raw['logger'] ?? []),
            queue: QueueConfig::fromArray($raw['queue'] ?? []),
            progress: ProgressConfig::fromArray($raw['progress'] ?? []),
            dbLog: DbLogConfig::fromArray($raw['dbLog'] ?? []),
            cleanup: CleanupConfig::fromArray($raw['cleanup'] ?? $raw['cleanTempFile'] ?? []),
        );
    }

    /**
     * Resolve driver configs, merging legacy `options` key into each driver
     * for backward compatibility with old config format.
     */
    private static function resolveDrivers(array $raw): array
    {
        $drivers = $raw['drivers'] ?? [];
        $options = $raw['options'] ?? [];

        if (empty($options)) {
            return $drivers;
        }

        $resolved = [];
        foreach ($drivers as $name => $driverConfig) {
            $resolved[$name] = array_replace_recursive($options, $driverConfig);
        }
        return $resolved;
    }

    public function getDriverConfig(string $name): array
    {
        return $this->drivers[$name] ?? [];
    }
}
