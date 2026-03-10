<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

class ListenerRegistrar
{
    /**
     * 默认的 Listener 类列表
     *
     * @return array<class-string<AbstractBaseListener>>
     */
    public static function getDefaultListeners(): array
    {
        return [
            ProgressListener::class,
            ExcelLogDbListener::class,
        ];
    }
}
