<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * 日志配置。
 *
 * 控制 Excel 组件使用哪个日志 channel 输出运行日志。
 * channel 值对应框架日志系统中的 channel 名称：
 *  - Laravel: config/logging.php 中的 channels key
 *  - Hyperf:  config/autoload/logger.php 中的 channel name
 *
 * 兼容旧配置键 'name'（已废弃，请使用 'channel'）。
 */
final class LoggingConfig
{
    public function __construct(
        /** 日志 channel 名称 */
        public readonly string $channel = 'excel',
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            channel: $raw['channel'] ?? $raw['name'] ?? 'excel',
        );
    }
}
