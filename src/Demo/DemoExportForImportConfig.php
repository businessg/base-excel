<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Demo;

use BusinessG\BaseExcel\Data\Export\Column;
use BusinessG\BaseExcel\Data\Export\ExportCallbackParam;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\Sheet;

/**
 * Demo 导出供导入测试的 Excel 数据文件。
 *
 * 同步导出 + 上传到 filesystem，生成包含 5 条姓名/邮箱示例数据的文件，
 * 结构与 DemoImportConfig 的 headerIndex=1 匹配，可直接用于导入测试。
 *
 * 测试方式:
 *   HTTP: POST {prefix}/excel/export  body: {"business_id": "demoExportForImport"}
 *         → 返回文件路径，用该路径调用导入接口
 *   CLI:  php artisan excel:export demoExportForImport            (Laravel)
 *         php bin/hyperf.php excel:export demoExportForImport     (Hyperf)
 */
class DemoExportForImportConfig extends ExportConfig
{
    public string $serviceName = 'Demo导入测试数据';

    public string $outPutType = self::OUT_PUT_TYPE_UPLOAD;

    public bool $isAsync = false;

    public function getSheets(): array
    {
        $this->setSheets([
            new Sheet([
                'name' => 'sheet1',
                'columns' => [
                    new Column(['title' => '姓名', 'field' => 'name']),
                    new Column(['title' => '邮箱', 'field' => 'email']),
                ],
                'count' => 5,
                'data' => [$this, 'getData'],
                'pageSize' => 10,
            ]),
        ]);
        return $this->sheets;
    }

    public function getData(ExportCallbackParam $param): array
    {
        $samples = [
            ['name' => '张三', 'email' => 'zhangsan@example.com'],
            ['name' => '李四', 'email' => 'lisi@example.com'],
            ['name' => '王五', 'email' => 'wangwu@example.com'],
            ['name' => '赵六', 'email' => 'zhaoliu@example.com'],
            ['name' => '钱七', 'email' => 'qianqi@example.com'],
        ];

        $offset = ($param->page - 1) * $param->pageSize;
        return array_slice($samples, $offset, $param->pageSize);
    }
}
