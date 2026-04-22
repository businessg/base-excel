<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * 监听器类名列表合并：将「config 中追加的类名」与「代码中默认类名」合并并去重。
 *
 * 规则：先默认、后 config；同类名只保留**首次**出现。Laravel / Hyperf 等均在各自
 * {@see ListenersConfig} 与各框架的监听器配置中维护「默认 + config」的拆解，再经本类统一合并。
 */
final class ListenerClassListMerge
{
    /**
     * 清洗配置中的 listeners 数组：
     *  - 非数组 / 空数组 → 返回 []
     *  - 逐项过滤非字符串与空串
     *  - 保留原顺序，不做与代码默认的合并
     *
     * 各框架在收集 `listeners` 配置段时可复用此方法，避免重复实现清洗逻辑。
     *
     * @param mixed $configured 原始配置值（通常是 `$excel['listeners']`）
     *
     * @return array<int, string>
     */
    public static function normalize(mixed $configured): array
    {
        if (! is_array($configured) || $configured === []) {
            return [];
        }

        return array_values(array_filter(
            $configured,
            static fn (mixed $c): bool => is_string($c) && $c !== ''
        ));
    }

    /**
     * @param array<int, class-string> $codeDefaults 代码默认（顺序优先）
     * @param array<int, class-string> $configExtras  配置中声明的类名，可为空
     *
     * @return array<int, class-string>
     */
    public static function merge(array $codeDefaults, array $configExtras): array
    {
        $merged = array_merge($codeDefaults, $configExtras);
        $seen = [];
        $out = [];
        foreach ($merged as $class) {
            if (! is_string($class) || $class === '' || isset($seen[$class])) {
                continue;
            }
            $seen[$class] = true;
            $out[] = $class;
        }

        return $out;
    }
}
