<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

interface DeferInterface
{
    /**
     * 延迟执行回调
     * 默认同步执行；Hyperf 等协程框架可使用 Coroutine::defer
     */
    public function defer(callable $callback): void;
}
