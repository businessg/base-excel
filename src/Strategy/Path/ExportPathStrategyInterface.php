<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Strategy\Path;

use BusinessG\BaseExcel\Data\Export\ExportConfig;

interface ExportPathStrategyInterface
{
    public function getPath(ExportConfig $config, string $fileExt = 'xlsx'): string;
}
