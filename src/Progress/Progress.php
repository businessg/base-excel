<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\BaseObject;

/**
 * 进度实现，使用 ProgressStorageInterface 做存储，框架组件实现对应接口即可
 */
class Progress implements ProgressInterface
{
    protected array $config;

    public function __construct(
        protected ProgressStorageInterface $storage,
        array $config = []
    ) {
        $this->config = array_merge([
            'prefix' => 'BaseExcel',
            'expire' => 3600,
        ], $config);
    }

    public function initRecord(BaseConfig $config): ProgressRecord
    {
        $sheetListProgress = [];
        foreach ($config->getSheets() as $sheet) {
            $sheetListProgress[$sheet->name] = new ProgressData();
        }
        $progressRecord = new ProgressRecord([
            'sheetListProgress' => $sheetListProgress,
            'progress' => new ProgressData(),
        ]);
        $this->persistRecord($config->getToken(), $progressRecord);

        return $progressRecord;
    }

    public function getRecord(BaseConfig $config): ProgressRecord
    {
        $record = $this->fetchRecord($config->getToken());
        if (!$record) {
            $record = $this->initRecord($config);
        }
        return $record;
    }

    public function getRecordByToken(string $token): ?ProgressRecord
    {
        return $this->fetchRecord($token);
    }

    public function setSheetProgress(BaseConfig $config, string $sheetName, ProgressData $progressData): ProgressData
    {
        $progressRecord = $this->getRecord($config);
        $sheetProgress = $progressRecord->getProgressBySheet($sheetName);
        $sheetProgress->status = $progressData->status;
        if ($progressData->total > 0) {
            $sheetProgress->total = $progressData->total;
        }
        if ($progressData->progress > 0) {
            $sheetProgress->progress += $progressData->progress;
            if ($sheetProgress->total <= 0) {
                $sheetProgress->total = $sheetProgress->progress;
            }
            $progressRecord->progress->progress += $progressData->progress;
            if ($sheetProgress->progress == $sheetProgress->total) {
                $sheetProgress->status = ProgressData::PROGRESS_STATUS_END;
            }
        }
        if ($progressData->success > 0) {
            $sheetProgress->success += $progressData->success;
            $progressRecord->progress->success += $progressData->success;
        }
        if ($progressData->fail > 0) {
            $sheetProgress->fail += $progressData->fail;
            $progressRecord->progress->fail += $progressData->fail;
        }
        $progressRecord = $this->setProgressStatus($progressRecord);
        $progressRecord->setProgressBySheet($sheetName, $sheetProgress);
        $this->persistRecord($config->getToken(), $progressRecord);

        return $sheetProgress;
    }

    public function setProgress(BaseConfig $config, ProgressData $progressData, ?BaseObject $data = null): ProgressRecord
    {
        $progressRecord = $this->getRecord($config);
        $progressRecord->progress->status = $progressData->status;
        if ($progressData->total > 0) {
            $progressRecord->progress->total = $progressData->total;
        }
        if ($progressData->progress > 0) {
            $progressRecord->progress->progress += $progressData->progress;
        }
        if ($progressData->success > 0) {
            $progressRecord->progress->success += $progressData->success;
        }
        if ($progressData->fail > 0) {
            $progressRecord->progress->fail += $progressData->fail;
        }
        if (!empty($progressData->message)) {
            $progressRecord->progress->message = $progressData->message;
        }
        if ($data !== null) {
            $progressRecord->data = $data;
        }
        $this->persistRecord($config->getToken(), $progressRecord);

        return $progressRecord;
    }

    public function pushMessage(string $token, string $message): void
    {
        if ($message === '' || trim($message) === '') {
            return;
        }
        $key = $this->getMessageKey($token);
        $ttl = (int) ($this->config['expire'] ?? 3600);
        $this->storage->lpush($key, $message, $ttl);
    }

    public function popMessage(string $token, int $num): array
    {
        $messages = [];
        $key = $this->getMessageKey($token);
        for ($i = 0; $i < $num; $i++) {
            $message = $this->storage->rpop($key);
            if ($message === null) {
                break;
            }
            if ($message !== '' && trim($message) !== '') {
                $messages[] = $message;
            }
        }
        return $messages;
    }

    public function peekMessage(string $token, int $num): array
    {
        $key = $this->getMessageKey($token);
        $raw = $this->storage->lrange($key, -$num, -1);
        $messages = [];
        foreach ($raw as $message) {
            if ($message !== '' && trim($message) !== '') {
                $messages[] = $message;
            }
        }
        return $messages;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function setProgressStatus(ProgressRecord $progressRecord): ProgressRecord
    {
        $total = 0;
        $status = array_map(function ($item) use (&$total) {
            $total += $item->total;
            return $item->status;
        }, $progressRecord->sheetListProgress);
        $status = array_unique($status);
        $count = count($status);
        if ($count <= 1) {
            $progressRecord->progress->status = current($status);
        } else {
            $progressRecord->progress->status = ProgressData::PROGRESS_STATUS_PROCESS;
        }
        $progressRecord->progress->total = $total;

        return $progressRecord;
    }

    protected function fetchRecord(string $token): ?ProgressRecord
    {
        $key = $this->getProgressKey($token);
        $raw = $this->storage->get($key);
        if ($raw === null || $raw === '') {
            return null;
        }
        $record = unserialize($raw);
        return $record instanceof ProgressRecord ? $record : null;
    }

    protected function persistRecord(string $token, ProgressRecord $progressRecord): void
    {
        $key = $this->getProgressKey($token);
        $ttl = (int) ($this->config['expire'] ?? 3600);
        $this->storage->set($key, serialize($progressRecord), $ttl);
    }

    protected function getProgressKey(string $token): string
    {
        return sprintf('%s_progress:%s', $this->config['prefix'] ?? 'BaseExcel', $token);
    }

    protected function getMessageKey(string $token): string
    {
        return sprintf('%s_message:%s', $this->config['prefix'] ?? 'BaseExcel', $token);
    }
}
