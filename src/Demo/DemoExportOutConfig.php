<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Demo;

use BusinessG\BaseExcel\Data\Export\Column;
use BusinessG\BaseExcel\Data\Export\ExportCallbackParam;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\Sheet;

/**
 * Demo 同步导出 + 直接输出（OUT_PUT_TYPE_OUT）。
 *
 * 浏览器访问即触发文件下载，不经过 filesystem 上传。
 * 适用于小数据量、需要即时下载的场景。
 *
 * 测试方式:
 *   HTTP: GET {prefix}/excel/export?business_id=demoExportOut
 *   CLI:  php artisan excel:export demoExportOut            (Laravel)
 *         php bin/hyperf.php excel:export demoExportOut     (Hyperf)
 */
class DemoExportOutConfig extends ExportConfig
{
    public string $serviceName = 'Demo导出（直接输出）';

    public string $outPutType = self::OUT_PUT_TYPE_OUT;

    public bool $isAsync = false;

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
                'count' => 20,
                'data' => [$this, 'getData'],
                'pageSize' => 100,
            ]),
        ]);
        return $this->sheets;
    }

    public function getData(ExportCallbackParam $param): array
    {
        $data = [];
        for ($i = 0; $i < $param->pageSize; $i++) {
            $index = ($param->page - 1) * $param->pageSize + $i + 1;
            if ($index > 20) {
                break;
            }
            $data[] = [
                'id' => $index,
                'name' => 'User' . $index,
                'email' => 'user' . $index . '@example.com',
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        return $data;
    }
}
