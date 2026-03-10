<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Demo;

use BusinessG\BaseExcel\Data\Export\Column;
use BusinessG\BaseExcel\Data\Export\ExportCallbackParam;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\Sheet;

/**
 * 内置 Demo 导出配置，用于快速验证导出功能是否正常。
 *
 * 生成 100 条虚拟用户数据（ID、姓名、邮箱、创建时间），同步导出并上传到 filesystem。
 *
 * 测试方式:
 *   HTTP: POST {prefix}/excel/export  body: {"business_id": "demoExport"}
 *   CLI:  php artisan excel:export DemoExport            (Laravel)
 *         php bin/hyperf.php excel:export DemoExport     (Hyperf)
 */
class DemoExportConfig extends ExportConfig
{
    public string $serviceName = 'Demo数据导出';

    public string $outPutType = self::OUT_PUT_TYPE_UPLOAD;

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
                'count' => 100,
                'data' => [$this, 'getData'],
                'pageSize' => 500,
            ]),
        ]);
        return $this->sheets;
    }

    public function getData(ExportCallbackParam $param): array
    {
        $data = [];
        for ($i = 0; $i < $param->pageSize; $i++) {
            $index = ($param->page - 1) * $param->pageSize + $i + 1;
            if ($index > 100) {
                break;
            }
            $data[] = [
                'id' => $index,
                'name' => 'User' . $index,
                'email' => 'user' . $index . '@example.com',
                'created_at' => date('Y-m-d H:i:s', time() - rand(0, 86400 * 30)),
            ];
        }
        return $data;
    }
}
