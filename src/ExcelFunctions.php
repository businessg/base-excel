<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel;

use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\ExportData;
use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\Data\Import\ImportData;
use BusinessG\BaseExcel\Progress\ProgressRecord;
use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * 框架无关的 Excel 辅助函数实现，框架通过 setContainerResolver 注入容器获取方式
 */
class ExcelFunctions
{
    private static ?\Closure $containerResolver = null;

    /**
     * 设置容器解析器，框架在启动时调用
     *
     * @param callable(): ContainerInterface $resolver
     */
    public static function setContainerResolver(callable $resolver): void
    {
        self::$containerResolver = $resolver instanceof \Closure ? $resolver : \Closure::fromCallable($resolver);
    }

    /**
     * 获取容器，未设置 resolver 时抛出异常
     */
    protected static function getContainer(): ContainerInterface
    {
        if (self::$containerResolver === null) {
            throw new RuntimeException('Excel container resolver is not set. Call ExcelFunctions::setContainerResolver() during bootstrap.');
        }
        return (self::$containerResolver)();
    }

    /**
     * 检查容器是否可用（Hyperf 等需在协程环境）
     */
    public static function hasContainer(): bool
    {
        if (self::$containerResolver === null) {
            return false;
        }
        try {
            (self::$containerResolver)();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function export(ExportConfig $config): ExportData
    {
        return self::getExcel()->export($config);
    }

    public static function import(ImportConfig $config): ImportData
    {
        return self::getExcel()->import($config);
    }

    public static function progressPopMessage(string $token, int $num, bool &$isEnd): array
    {
        return self::getExcel()->popMessageAndIsEnd($token, $num, $isEnd);
    }

    public static function progressPushMessage(string $token, string $message): void
    {
        self::getExcel()->pushMessage($token, $message);
    }

    public static function progress(string $token): ?ProgressRecord
    {
        return self::getExcel()->getProgressRecord($token);
    }

    private static function getExcel(): ExcelInterface
    {
        $container = self::getContainer();
        if (!$container->has(ExcelInterface::class)) {
            throw new RuntimeException('ExcelInterface is missing in container.');
        }
        return $container->get(ExcelInterface::class);
    }
}
