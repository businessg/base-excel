<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

final class DbLogConfig
{
    public function __construct(
        public readonly bool $enabled = true,
        public readonly ?string $model = null,
    ) {
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            enabled: $raw['enabled'] ?? $raw['enable'] ?? true,
            model: $raw['model'] ?? null,
        );
    }
}
