<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Demo;

use BusinessG\BaseExcel\Data\Export\Column;
use BusinessG\BaseExcel\Data\Export\ExportCallbackParam;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\Sheet;
use BusinessG\BaseExcel\ExcelFunctions;

/**
 * Demo 异步导出 + 上传到 filesystem（OUT_PUT_TYPE_UPLOAD）。
 *
 * 模拟 5 万条大数据量导出，通过队列异步处理，支持进度查询和消息推送。
 * 每页回调中 usleep 模拟耗时，并推送当前页码消息用于验证进度功能。
 *
 * 测试方式:
 *   HTTP: POST {prefix}/excel/export  body: {"business_id": "demoAsyncExport"}
 *         → 返回 token，前端轮询 progress / message 接口
 *   CLI:  php artisan excel:export demoAsyncExport            (Laravel)
 *         php bin/hyperf.php excel:export demoAsyncExport     (Hyperf)
 */
class DemoAsyncExportConfig extends ExportConfig
{
    public string $serviceName = 'Demo导出（异步）';

    public string $outPutType = self::OUT_PUT_TYPE_UPLOAD;

    public bool $isAsync = true;

    public function getSheets(): array
    {
        $this->setSheets([
            new Sheet([
                'name' => 'sheet1',
                'columns' => [
                    new Column(['title' => 'ID', 'field' => 'id']),
                    new Column(['title' => '姓名', 'field' => 'name']),
                    new Column(['title' => '邮箱', 'field' => 'email']),
                    new Column(['title' => '创建时间', 'field' => 'created_at']),
                ],
                'count' => 50000,
                'data' => [$this, 'getData'],
                'pageSize' => 1000,
            ]),
        ]);
        return $this->sheets;
    }

    public function getData(ExportCallbackParam $param): array
    {
        usleep(100000);

        $data = [];
        $totalCount = 50000;
        for ($i = 0; $i < $param->pageSize; $i++) {
            $index = ($param->page - 1) * $param->pageSize + $i + 1;
            if ($index > $totalCount) {
                break;
            }
            $data[] = [
                'id' => $index,
                'name' => 'User' . $index,
                'email' => 'user' . $index . '@example.com',
                'created_at' => date('Y-m-d H:i:s', time() - rand(0, 86400 * 30)),
            ];
        }

        if (ExcelFunctions::hasContainer()) {
            ExcelFunctions::progressPushMessage(
                $param->config->getToken(),
                sprintf('当前第%s页, 数量:%s', $param->page, $param->pageSize)
            );
        }

        return $data;
    }
}
