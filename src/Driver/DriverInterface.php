<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Driver;

use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\ExportData;
use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\Data\Import\ImportData;

interface DriverInterface
{
    public function export(ExportConfig $config): ExportData;

    public function import(ImportConfig $config): ImportData;

    public function getConfig(): array;

    public function getTempDir(): string;

    public function getTempFileName(): string;
}
