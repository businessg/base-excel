<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

use BusinessG\BaseExcel\Config\ListenersConfig;

class ListenerRegistrar
{
    /**
     * Base 侧默认监听器类名（与 {@see ListenersConfig} 默认值一致）。
     *
     * @return array<class-string<AbstractBaseListener>>
     */
    public static function getDefaultListeners(): array
    {
        return ListenersConfig::defaultBaseClassNames();
    }

    /**
     * 从 excel 配置数组解析监听器类名：`listeners` 非空则使用，否则使用 {@see ListenersConfig} 默认值。
     *
     * @param array<string, mixed> $excelConfig
     *
     * @return array<class-string<AbstractBaseListener>>
     */
    public static function resolveListeners(array $excelConfig): array
    {
        return ListenersConfig::fromExcelArray($excelConfig)->classNames;
    }
}
