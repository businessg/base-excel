<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

use BusinessG\BaseExcel\Listener\ExcelLogDbListener;
use BusinessG\BaseExcel\Listener\ProgressListener;

/**
 * excel 配置中的 `listeners` 键：类名字符串列表（含义由集成层约定）。
 *
 * 配置中的 `listeners` 仅填**追加**的监听器类名，会与 {@see self::DEFAULT_CLASS_NAMES}
 * 经 {@see ListenerClassListMerge::merge()} **合并、按序去重**（默认在前、配置在后）。
 * 未配置或 `[]` 时仅使用默认监听器。
 */
final class ListenersConfig
{
    /**
     * Base 层内置默认监听器类名。
     *
     * 默认启用：
     *  - {@see ProgressListener}     进度追踪（响应进度事件，写入 Redis）
     *  - {@see ExcelLogDbListener}   数据库日志（响应导入导出事件，写入 excel_log 表）
     *
     * @var array<int, class-string>
     */
    public const DEFAULT_CLASS_NAMES = [
        ProgressListener::class,
        ExcelLogDbListener::class,
    ];

    /**
     * @param array<int, class-string> $classNames
     */
    public function __construct(
        public readonly array $classNames = self::DEFAULT_CLASS_NAMES,
    ) {
    }

    /**
     * 从顶层 excel 配置数组解析监听器：默认 + 配置追加，按序去重。
     *
     * @param array<string, mixed> $excel 顶层 excel 配置数组（如 config('excel')）
     */
    public static function fromExcelArray(array $excel): self
    {
        if (! array_key_exists('listeners', $excel)) {
            return new self();
        }

        $extra = ListenerClassListMerge::normalize($excel['listeners']);
        if ($extra === []) {
            return new self();
        }

        return new self(
            classNames: ListenerClassListMerge::merge(self::DEFAULT_CLASS_NAMES, $extra)
        );
    }

    /**
     * @return array<int, class-string>
     */
    public static function defaultBaseClassNames(): array
    {
        return self::DEFAULT_CLASS_NAMES;
    }
}
