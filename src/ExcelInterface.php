<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel;

use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\ExportData;
use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\Data\Import\ImportData;
use BusinessG\BaseExcel\Driver\DriverInterface;
use BusinessG\BaseExcel\Progress\ProgressRecord;

interface ExcelInterface
{
    public function export(ExportConfig $config): ExportData;

    public function import(ImportConfig $config): ImportData;

    public function getProgressRecord(string $token): ?ProgressRecord;

    public function popMessage(string $token, int $num = 50): array;

    public function popMessageAndIsEnd(string $token, int $num = 50, bool &$isEnd = true): array;

    public function pushMessage(string $token, string $message): void;

    public function getDefaultDriver(): DriverInterface;

    public function getDriverByName(string $driverName): DriverInterface;

    public function getDriver(?string $driverName = null): DriverInterface;

    public function getConfig(): array;
}
