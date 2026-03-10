<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

final class QueueConfig
{
    public function __construct(
        public readonly string $connection = 'default',
        public readonly string $channel = 'default',
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            connection: $raw['connection'] ?? $raw['name'] ?? 'default',
            channel: $raw['channel'] ?? 'default',
        );
    }
}
