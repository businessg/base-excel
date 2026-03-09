<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress\Storage;

use BusinessG\BaseExcel\Progress\ProgressStorageInterface;

/**
 * 进度存储抽象基类，提供 Lua 脚本等公共能力
 */
abstract class AbstractProgressStorage implements ProgressStorageInterface
{
    /**
     * LPUSH + EXPIRE 原子操作的 Lua 脚本
     * KEYS[1]: key, ARGV[1]: value, ARGV[2]: ttl
     */
    protected static function getLpushLuaScript(): string
    {
        return <<<'LUA'
        redis.call('LPUSH', KEYS[1], ARGV[1])
        redis.call('EXPIRE', KEYS[1], ARGV[2])
        return 1
LUA;
    }
}
