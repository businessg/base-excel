<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Demo;

use BusinessG\BaseExcel\Data\Export\Column;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\Sheet;
use BusinessG\BaseExcel\Data\Export\Style;

/**
 * 内置 Demo 导入模板导出配置。
 *
 * 同步导出 + 直接响应输出（OUT_PUT_TYPE_OUT），浏览器访问即下载模板文件。
 * 模板包含填写说明行和列标题行（姓名、邮箱），无数据行。
 *
 * 典型用法（动态模板 URL）:
 *   GET {prefix}/excel/export?business_id=demoImportTemplate
 *   浏览器直接下载 xlsx 文件。
 */
class DemoImportTemplateExportConfig extends ExportConfig
{
    public string $serviceName = 'Demo导入模板';

    public bool $isAsync = false;

    public string $outPutType = self::OUT_PUT_TYPE_OUT;

    public function getSheets(): array
    {
        $this->setSheets([
            new Sheet([
                'name' => 'sheet1',
                'columns' => [
                    new Column([
                        'title' => implode("\n", [
                            '1、姓名：必填，字符串类型',
                            '2、邮箱：必填，必须是有效的邮箱格式',
                            '3、请按照模板格式填写数据',
                        ]),
                        'field' => 'name',
                        'height' => 58,
                        'headerStyle' => new Style([
                            'wrap' => true,
                            'fontColor' => 0x2972F4,
                            'font' => '等线',
                            'align' => [Style::FORMAT_ALIGN_LEFT, Style::FORMAT_ALIGN_VERTICAL_CENTER],
                            'fontSize' => 10,
                            'bold' => true,
                        ]),
                        'children' => [
                            new Column([
                                'title' => '姓名',
                                'field' => 'name',
                                'width' => 32,
                                'headerStyle' => new Style([
                                    'align' => [Style::FORMAT_ALIGN_CENTER],
                                    'bold' => true,
                                ]),
                            ]),
                            new Column([
                                'title' => '邮箱',
                                'field' => 'email',
                                'width' => 40,
                                'headerStyle' => new Style([
                                    'align' => [Style::FORMAT_ALIGN_CENTER],
                                    'bold' => true,
                                ]),
                            ]),
                        ],
                    ]),
                ],
                'count' => 0,
                'data' => [],
                'pageSize' => 1,
            ]),
        ]);
        return $this->sheets;
    }
}
