<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

final class CleanupConfig
{
    public function __construct(
        public readonly bool $enabled = true,
        public readonly int $maxAge = 1800,
        public readonly int $interval = 3600,
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            enabled: $raw['enabled'] ?? $raw['enable'] ?? true,
            maxAge: $raw['maxAge'] ?? $raw['time'] ?? 1800,
            interval: $raw['interval'] ?? 3600,
        );
    }
}
