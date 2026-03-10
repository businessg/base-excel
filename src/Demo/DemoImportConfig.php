<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Demo;

use BusinessG\BaseExcel\Data\Import\Column;
use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\Data\Import\ImportRowCallbackParam;
use BusinessG\BaseExcel\Data\Import\Sheet;
use BusinessG\BaseExcel\Exception\ExcelException;
use BusinessG\BaseExcel\ExcelFunctions;

/**
 * 内置 Demo 导入配置，用于快速验证导入功能是否正常。
 *
 * 要求 Excel 文件第 1 行为表头（姓名、邮箱），逐行校验并通过 pushMessage 输出处理结果。
 *
 * 测试方式:
 *   1. 先导出模板: POST {prefix}/excel/export  body: {"business_id": "demoExportForImport"}
 *   2. 上传文件:   POST {prefix}/excel/upload  file: xxx.xlsx
 *   3. 执行导入:   POST {prefix}/excel/import  body: {"business_id": "demoImport", "url": "/path/to/file.xlsx"}
 *   CLI: php artisan excel:import demoImport --path=/path/to/file.xlsx     (Laravel)
 *        php bin/hyperf.php excel:import demoImport --path=/path/to/file.xlsx (Hyperf)
 */
class DemoImportConfig extends ImportConfig
{
    public string $serviceName = 'Demo数据导入';

    public bool $isAsync = false;

    protected array $importedData = [];

    public function getSheets(): array
    {
        $this->setSheets([
            new Sheet([
                'name' => 'sheet1',
                'headerIndex' => 1,
                'columns' => [
                    new Column(['title' => '姓名', 'field' => 'name']),
                    new Column(['title' => '邮箱', 'field' => 'email']),
                ],
                'callback' => [$this, 'rowCallback'],
            ]),
        ]);
        return $this->sheets;
    }

    public function rowCallback(ImportRowCallbackParam $param): void
    {
        if (empty($param->row)) {
            return;
        }

        $name = $param->row['name'] ?? '';
        $email = $param->row['email'] ?? '';
        $rowNum = $param->rowIndex + 2;

        if (empty($name)) {
            throw new ExcelException("第{$rowNum}行: 姓名不能为空");
        }
        if (empty($email)) {
            throw new ExcelException("第{$rowNum}行: 邮箱不能为空");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ExcelException("第{$rowNum}行: 邮箱格式不正确");
        }

        $this->importedData[] = [
            'name' => $name,
            'email' => $email,
            'row_index' => $param->rowIndex + 1,
        ];

        if (ExcelFunctions::hasContainer()) {
            ExcelFunctions::progressPushMessage(
                $param->config->getToken(),
                sprintf('第%s行: %s <%s> 导入成功', $rowNum, $name, $email)
            );
        }
    }

    public function getImportedData(): array
    {
        return $this->importedData;
    }
}
