<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\BaseObject;

interface ProgressInterface
{
    public function initRecord(BaseConfig $config): ProgressRecord;

    public function getRecord(BaseConfig $config): ProgressRecord;

    public function getRecordByToken(string $token): ?ProgressRecord;

    public function setSheetProgress(BaseConfig $config, string $sheetName, ProgressData $progressData): ProgressData;

    public function setProgress(BaseConfig $config, ProgressData $progressData, ?BaseObject $data = null): ProgressRecord;

    public function pushMessage(string $token, string $message): void;

    public function popMessage(string $token, int $num): array;

    /**
     * 调试用：读取消息（不消费），用于排查推送/获取问题
     */
    public function peekMessage(string $token, int $num): array;
}
