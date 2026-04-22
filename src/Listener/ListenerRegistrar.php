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
     * 从 excel 配置解析监听器类名。
     * 语义保持为“默认监听器 + 配置追加监听器”，并在解析阶段完成去重。
     *
     * 合并规则同 {@see ListenersConfig::fromExcelArray}（内部 {@see \BusinessG\BaseExcel\Config\ListenerClassListMerge}）。
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
