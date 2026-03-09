<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress\Storage;

use Predis\Client;

/**
 * 基于 Predis 的进度存储实现
 */
class PredisProgressStorage extends AbstractProgressStorage
{
    protected Client $client;

    public function __construct(array $config = [])
    {
        $parameters = [
            'scheme' => $config['scheme'] ?? 'tcp',
            'host' => $config['host'] ?? '127.0.0.1',
            'port' => (int) ($config['port'] ?? 6379),
        ];
        if (!empty($config['auth'])) {
            $parameters['password'] = $config['auth'];
        }
        if (isset($config['db']) && $config['db'] > 0) {
            $parameters['database'] = (int) $config['db'];
        }

        $options = $config['options'] ?? [];
        $this->client = new Client($parameters, $options);
    }

    public function get(string $key): ?string
    {
        $value = $this->client->get($key);
        return $value !== null ? (string) $value : null;
    }

    public function set(string $key, string $value, int $ttl): void
    {
        $this->client->setex($key, $ttl, $value);
    }

    public function lpush(string $key, string $value, int $ttl): void
    {
        $this->client->eval(static::getLpushLuaScript(), 1, $key, $value, $ttl);
    }

    public function rpop(string $key): ?string
    {
        $value = $this->client->rpop($key);
        return $value !== null ? (string) $value : null;
    }
}
