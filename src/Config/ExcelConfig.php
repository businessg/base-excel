<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * Typed configuration DTO that centralizes all defaults and provides
 * backward-compatible parsing from both old and new config formats.
 */
final class ExcelConfig
{
    public function __construct(
        public readonly string $default = 'xlswriter',
        public readonly array $drivers = [],
        public readonly LoggingConfig $logging = new LoggingConfig(),
        public readonly QueueConfig $queue = new QueueConfig(),
        public readonly ProgressConfig $progress = new ProgressConfig(),
        public readonly DbLogConfig $dbLog = new DbLogConfig(),
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
