<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

use BusinessG\BaseExcel\Data\BaseConfig;

interface ProgressInterface
{
    public function initRecord(BaseConfig $config): ProgressRecord;

    public function getRecord(BaseConfig $config): ProgressRecord;

    public function getRecordByToken(string $token): ?ProgressRecord;

    public function setSheetProgress(BaseConfig $config, string $sheetName, ProgressData $progressData): ProgressData;

    public function setProgress(BaseConfig $config, ProgressData $progressData): ProgressRecord;

    public function pushMessage(string $token, string $message): void;

    public function popMessage(string $token, int $num): array;
}
