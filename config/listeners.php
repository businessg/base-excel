<?php

declare(strict_types=1);

/**
 * 默认 Excel 事件监听器类列表（Laravel 等直接使用 BaseExcel 监听器的场景）。
 *
 * 应用可在 config/excel.php 的 listeners 中覆盖；未配置或为空时使用本文件。
 */
return [
    \BusinessG\BaseExcel\Listener\ProgressListener::class,
    \BusinessG\BaseExcel\Listener\ExcelLogDbListener::class,
];
