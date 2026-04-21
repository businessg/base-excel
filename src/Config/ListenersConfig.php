<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * excel 配置中的 `listeners` 键：类名字符串列表（含义由集成层约定）。
 *
 * Laravel：应为 {@see \BusinessG\BaseExcel\Listener\AbstractBaseListener} 子类；
 * 未配置或为空数组时，使用 {@see self::defaultBaseClassNames()}（进度、数据库日志）。
 *
 * 其他框架若在同名键中存放自身监听器类名，构建 {@see ExcelConfig} 时本对象会原样保留；
 * 请勿在 base-excel 中假定其必为 Base 监听器。
 */
final class ListenersConfig
{
    /**
     * @param array<int, class-string> $classNames
     */
    public function __construct(
        public readonly array $classNames = [
            \BusinessG\BaseExcel\Listener\ProgressListener::class,
            \BusinessG\BaseExcel\Listener\ExcelLogDbListener::class,
        ],
    ) {
    }

    /**
     * @param array<string, mixed> $excel 顶层 excel 配置数组（如 config('excel')）
     */
    public static function fromExcelArray(array $excel): self
    {
        $configured = $excel['listeners'] ?? null;
        if (is_array($configured) && $configured !== []) {
            $classes = array_values(array_filter(
                $configured,
                static fn (mixed $c): bool => is_string($c) && $c !== ''
            ));

            return new self(classNames: $classes);
        }

        return new self();
    }

    /**
     * @return array<int, class-string>
     */
    public static function defaultBaseClassNames(): array
    {
        return (new self())->classNames;
    }
}
