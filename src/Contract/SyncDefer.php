<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

class SyncDefer implements DeferInterface
{
    public function defer(callable $callback): void
    {
        $callback();
    }
}
