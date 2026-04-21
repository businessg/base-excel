<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

class ListenerRegistrar
{
    private const DEFAULT_CONFIG_PATH = __DIR__ . '/../../config/listeners.php';

    /**
     * 从包内默认配置文件加载的 Listener 类列表。
     *
     * @return array<class-string<AbstractBaseListener>>
     */
    public static function getDefaultListeners(): array
    {
        return self::loadDefaultListenersConfig();
    }

    /**
     * 从 excel 配置数组解析监听器类列表：`listeners` 非空则使用，否则回退到包内默认配置。
     *
     * @return array<class-string<AbstractBaseListener>>
     */
    public static function resolveListeners(array $excelConfig): array
    {
        $configured = $excelConfig['listeners'] ?? null;
        if (is_array($configured) && $configured !== []) {
            return array_values(array_filter(
                $configured,
                static fn (mixed $class): bool => is_string($class) && $class !== ''
            ));
        }

        return self::getDefaultListeners();
    }

    /**
     * @return array<class-string<AbstractBaseListener>>
     */
    private static function loadDefaultListenersConfig(): array
    {
        /** @var array<class-string<AbstractBaseListener>> */
        return require self::DEFAULT_CONFIG_PATH;
    }
}
