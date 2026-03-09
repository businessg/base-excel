<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

use BusinessG\BaseExcel\Progress\Storage\PhpRedisProgressStorage;
use BusinessG\BaseExcel\Progress\Storage\PredisProgressStorage;

/**
 * 进度存储工厂，根据配置创建 php-redis 或 predis 实现
 */
class ProgressStorageFactory
{
    public const DRIVER_PHP_REDIS = 'php-redis';
    public const DRIVER_PREDIS = 'predis';

    public static function create(array $config): ProgressStorageInterface
    {
        $driver = $config['driver'] ?? self::DRIVER_PHP_REDIS;
        $redisConfig = $config['redis'] ?? [];

        return match ($driver) {
            self::DRIVER_PREDIS => new PredisProgressStorage($redisConfig),
            default => new PhpRedisProgressStorage($redisConfig),
        };
    }
}
