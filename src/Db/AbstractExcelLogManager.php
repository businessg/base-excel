<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Db;

use BusinessG\BaseExcel\Data\BaseConfig;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Progress\ProgressData;
use BusinessG\BaseExcel\Progress\ProgressInterface;
use BusinessG\BaseExcel\Progress\ProgressRecord;
use Psr\Container\ContainerInterface;

/**
 * Excel 日志管理器抽象基类，框架只需实现 resolveConfig 和 performUpsert
 */
abstract class AbstractExcelLogManager implements ExcelLogInterface
{
    public const TYPE_EXPORT = 'export';
    public const TYPE_IMPORT = 'import';

    protected array $config;

    public function __construct(protected ContainerInterface $container, protected ProgressInterface $progress)
    {
        $this->config = $this->resolveConfig();
    }

    /**
     * 解析配置，框架实现
     */
    abstract protected function resolveConfig(): array;

    /**
     * 获取 Model 类名，由框架子类重写以提供默认值
     */
    abstract protected function getDefaultModelClass(): string;

    /**
     * 执行 upsert 操作，Laravel/Hyperf Eloquent API 兼容
     * 如果框架 ORM 不兼容，子类可重写此方法
     */
    protected function performUpsert(array $saveParam): int
    {
        $modelClass = $this->config['model'] ?? $this->getDefaultModelClass();
        return $modelClass::query()->upsert([$saveParam], ['token']);
    }

    public function saveLog(BaseConfig $config, array $saveParam = []): int
    {
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

    public function getProgressByToken(string $token): ?ProgressRecord
    {
        return $this->progress->getRecordByToken($token);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
