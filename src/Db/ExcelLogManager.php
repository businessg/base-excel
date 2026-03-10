<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Db;

use BusinessG\BaseExcel\Config\DbLogConfig;
use BusinessG\BaseExcel\Config\ExcelConfig;
use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Progress\ProgressData;
use BusinessG\BaseExcel\Progress\ProgressInterface;
use BusinessG\BaseExcel\Progress\ProgressRecord;
use Psr\Container\ContainerInterface;

class ExcelLogManager implements ExcelLogInterface
{
    public const TYPE_EXPORT = 'export';
    public const TYPE_IMPORT = 'import';

    protected DbLogConfig $dbLogConfig;
    protected ?ExcelLogRepositoryInterface $repository;

    public function __construct(
        protected ContainerInterface $container,
        protected ProgressInterface $progress,
        ConfigResolverInterface $configResolver
    ) {
        $this->dbLogConfig = ExcelConfig::fromArray($configResolver->get('excel', []))->dbLog;
        $this->repository = $container->has(ExcelLogRepositoryInterface::class)
            ? $container->get(ExcelLogRepositoryInterface::class)
            : null;
    }

    public function saveLog(BaseConfig $config, array $saveParam = []): int
    {
        if (!$this->dbLogConfig->enabled) {
            return 0;
        }

        $token = $config->getToken();
        $type = $config instanceof ExportConfig ? static::TYPE_EXPORT : static::TYPE_IMPORT;
        $progressRecord = $this->getProgressByToken($token);

        $saveParam = array_merge($saveParam, [
            'token' => $token,
            'config_class' => get_class($config),
            'config' => json_encode($config->__serialize()),
            'type' => $type,
            'service_name' => $config->serviceName,
            'progress' => json_encode($progressRecord?->progress),
            'sheet_progress' => json_encode($progressRecord?->sheetListProgress),
            'status' => $progressRecord?->progress->status ?: ProgressData::PROGRESS_STATUS_AWAIT,
            'data' => json_encode($progressRecord?->data ?: []),
        ]);
        if ($type === static::TYPE_EXPORT) {
            $saveParam['url'] = $progressRecord?->data?->response ?? '';
        } else {
            $saveParam['url'] = $config->getPath();
        }

        return $this->performUpsert($saveParam);
    }

    protected function performUpsert(array $saveParam): int
    {
        if ($this->repository) {
            return $this->repository->upsert($saveParam);
        }

        $modelClass = $this->dbLogConfig->model;
        if (!$modelClass) {
            return 0;
        }
        return $modelClass::query()->upsert([$saveParam], ['token']);
    }

    public function getProgressByToken(string $token): ?ProgressRecord
    {
        return $this->progress->getRecordByToken($token);
    }

    public function getConfig(): array
    {
        return [
            'enabled' => $this->dbLogConfig->enabled,
            'enable' => $this->dbLogConfig->enabled,
            'model' => $this->dbLogConfig->model,
        ];
    }
}
