<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Service;

use BusinessG\BaseExcel\Config\ExcelConfig;
use BusinessG\BaseExcel\Config\HttpConfig;
use BusinessG\BaseExcel\Contract\Arrayable;
use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\ExcelInterface;
use BusinessG\BaseExcel\Exception\ExcelException;
use BusinessG\BaseExcel\Progress\ProgressData;
use BusinessG\BaseExcel\Progress\ProgressRecord;

/**
 * 通用 Excel 业务服务。
 *
 * 通过 businessId 查找 excel_business 配置中的导入/导出配置类，
 * 调用 ExcelInterface 执行操作。框架无关，不依赖任何框架的 Request/Response。
 */
class ExcelBusinessService
{
    public function __construct(
        protected ExcelInterface $excel,
        protected ConfigResolverInterface $configResolver
    ) {
    }

    public function getProgressByToken(string $token): ?ProgressRecord
    {
        return $this->excel->getProgressRecord($token);
    }

    /**
     * @return array 进度记录数组（已过滤不可序列化对象）
     * @throws ExcelException
     */
    public function getProgressArrayByToken(string $token): array
    {
        $record = $this->getProgressByToken($token)?->toArray();
        if (!$record) {
            throw new ExcelException('对应记录不存在');
        }
        return $this->sanitizeForJson($record);
    }

    /**
     * @return array{isEnd: bool, message: array}
     * @throws ExcelException
     */
    public function getMessageByToken(string $token, int $num = 50): array
    {
        $record = $this->getProgressByToken($token);
        $message = $this->excel->popMessage($token, $num);

        if (!$record) {
            throw new ExcelException('对应记录不存在');
        }

        return [
            'isEnd' => empty($message) && in_array($record->progress->status ?? 0, [
                ProgressData::PROGRESS_STATUS_COMPLETE,
                ProgressData::PROGRESS_STATUS_FAIL,
                ProgressData::PROGRESS_STATUS_END,
            ]),
            'message' => $message,
        ];
    }

    /**
     * 获取导入业务的附加信息，并自动解析模板下载地址。
     *
     * info 配置支持两种模板地址方式（二选一）:
     *
     *  1. templateUrl — 绝对 URL，直接返回
     *     'templateUrl' => 'https://cdn.example.com/templates/order.xlsx'
     *
     *  2. templateBusinessId — 导出业务 ID，自动拼接完整下载地址
     *     'templateBusinessId' => 'demoImportTemplate'
     *     返回时自动构建为: {domain}/{prefix}/excel/export?business_id=demoImportTemplate
     *     （对应的导出配置需 isAsync=false + OUT_PUT_TYPE_OUT）
     *
     * @return array 业务 info 配置（templateUrl 为最终完整地址）
     */
    public function getInfoByBusinessId(string $businessId): array
    {
        $config = $this->getImportConfig($businessId);
        $info = $config['info'] ?? [];

        if (!empty($info['templateBusinessId']) && empty($info['templateUrl'])) {
            $info['templateUrl'] = $this->buildExportUrl($info['templateBusinessId']);
        }

        unset($info['templateBusinessId']);

        return $info;
    }

    /**
     * 根据导出业务 ID 构建完整的导出下载地址。
     * 格式: {domain}/{prefix}/excel/export?business_id={businessId}
     */
    protected function buildExportUrl(string $businessId): string
    {
        $hc = $this->getHttpConfig();
        $prefix = trim($hc->prefix, '/');
        $path = ($prefix !== '' ? $prefix . '/' : '') . 'excel/export?business_id=' . urlencode($businessId);
        $domain = rtrim($hc->domain, '/');

        if ($domain !== '') {
            return $domain . '/' . $path;
        }

        return '/' . $path;
    }

    /**
     * 构建成功响应数组，字段名由 http 配置决定。
     */
    public function successResponse(mixed $data = null, string $message = ''): array
    {
        $hc = $this->getHttpConfig();
        $resp = [
            $hc->codeField => $hc->successCode,
        ];
        if ($data !== null) {
            $resp[$hc->dataField] = $data;
        }
        if ($message !== '') {
            $resp[$hc->messageField] = $message;
        }
        return $resp;
    }

    /**
     * 构建错误响应数组，字段名由 http 配置决定。
     */
    public function errorResponse(int|string $code, string $message = '', mixed $data = null): array
    {
        $hc = $this->getHttpConfig();
        $resp = [
            $hc->codeField => $code,
            $hc->messageField => $message,
        ];
        if ($data !== null) {
            $resp[$hc->dataField] = $data;
        }
        return $resp;
    }

    protected ?HttpConfig $httpConfigCache = null;

    protected function getHttpConfig(): HttpConfig
    {
        if ($this->httpConfigCache === null) {
            $raw = $this->configResolver->get('excel') ?? [];
            $this->httpConfigCache = ExcelConfig::fromArray(is_array($raw) ? $raw : [])->http;
        }
        return $this->httpConfigCache;
    }

    protected function getDomain(): string
    {
        return $this->getHttpConfig()->domain;
    }

    /**
     * @return array{token: string, response: mixed}
     * @throws ExcelException
     */
    public function exportByBusinessId(string $businessId, array $param = []): array
    {
        $config = $this->getExportConfig($businessId);
        if (!$config) {
            throw new ExcelException('对应业务ID不存在: ' . $businessId);
        }

        $configInstance = new $config['config']([
            'params' => $param,
        ]);
        $data = $this->excel->export($configInstance);

        return [
            'token' => $data->token,
            'response' => $data->getResponse(),
        ];
    }

    /**
     * @return array{token: string}
     * @throws ExcelException
     */
    public function importByBusinessId(string $businessId, string $path): array
    {
        $config = $this->getImportConfig($businessId);
        if (!$config) {
            throw new ExcelException('对应业务ID不存在: ' . $businessId);
        }

        $importConfig = new $config['config']();
        $importConfig->setPath($path);
        $data = $this->excel->import($importConfig);

        return [
            'token' => $data->token,
        ];
    }

    protected function getExportConfig(string $businessId): ?array
    {
        return $this->configResolver->get('excel_business.export.' . $businessId);
    }

    protected function getImportConfig(string $businessId): ?array
    {
        return $this->configResolver->get('excel_business.import.' . $businessId);
    }

    /**
     * 递归过滤不可 JSON 序列化的对象（如 Response、StreamedResponse），
     * Arrayable 对象转为数组保留，其余对象置 null。
     */
    protected function sanitizeForJson(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->sanitizeForJson($v), $value);
        }
        if (is_object($value)) {
            if ($value instanceof Arrayable) {
                return $this->sanitizeForJson($value->toArray());
            }
            return null;
        }
        return $value;
    }
}
