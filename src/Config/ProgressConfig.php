<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

final class ProgressConfig
{
    public function __construct(
        public readonly bool $enabled = true,
        public readonly string $prefix = 'Excel',
        public readonly int $ttl = 3600,
        public readonly string $connection = 'default',
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            enabled: $raw['enabled'] ?? $raw['enable'] ?? true,
            prefix: $raw['prefix'] ?? 'Excel',
            ttl: $raw['ttl'] ?? $raw['expire'] ?? 3600,
            connection: $raw['connection'] ?? $raw['redis']['connection'] ?? $raw['redis']['pool'] ?? 'default',
        );
    }
}
