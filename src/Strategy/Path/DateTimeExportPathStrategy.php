<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Strategy\Path;

use BusinessG\BaseExcel\Data\Export\ExportConfig;

class DateTimeExportPathStrategy implements ExportPathStrategyInterface
{
    public function getPath(ExportConfig $config, string $fileExt = 'xlsx'): string
    {
        return $config->getServiceName() . '_' . date('YmdHis') . '.' . $fileExt;
    }
}
