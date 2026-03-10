<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

final class LoggingConfig
{
    public function __construct(
        public readonly string $channel = 'excel',
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            channel: $raw['channel'] ?? $raw['name'] ?? 'excel',
        );
    }
}
