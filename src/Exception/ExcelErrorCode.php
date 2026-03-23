<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Exception;

/**
 * Excel 组件统一错误状态码（五位数）。
 *
 * 分类规则:
 *   0       — 成功
 *   10xxx   — 请求参数错误
 *   20xxx   — 业务配置错误
 *   30xxx   — 文件操作错误
 *   40xxx   — Excel 处理错误
 *   50xxx   — 驱动/系统错误
 */
final class ExcelErrorCode
{
    /** 成功 */
    public const SUCCESS = 0;

    // ---- 10xxx 请求参数错误 ----

    /** business_id 必填 */
    public const BUSINESS_ID_REQUIRED = 10001;

    /** 上传文件无效 */
    public const UPLOAD_FILE_INVALID = 10002;

    /** 文件格式不支持（仅 xlsx/xls） */
    public const UPLOAD_FILE_FORMAT_UNSUPPORTED = 10003;

    // ---- 20xxx 业务配置错误 ----

    /** 业务ID不存在 */
    public const BUSINESS_ID_NOT_FOUND = 20001;

    /** 进度/消息记录不存在 */
    public const PROGRESS_RECORD_NOT_FOUND = 20002;

    /** 异步不支持 OUT 直接输出类型 */
    public const ASYNC_OUT_NOT_SUPPORTED = 20003;

    /** 异步不支持返回 sheet 数据 */
    public const ASYNC_RETURN_SHEET_NOT_SUPPORTED = 20004;

    // ---- 30xxx 文件操作错误 ----

    /** 文件路径不存在 */
    public const FILE_PATH_NOT_EXISTS = 30001;

    /** 文件复制失败 */
    public const FILE_COPY_FAILED = 30002;

    /** 文件下载失败 */
    public const FILE_DOWNLOAD_FAILED = 30003;

    /** 临时文件创建失败 */
    public const TEMP_FILE_CREATE_FAILED = 30004;

    /** 临时目录创建失败 */
    public const TEMP_DIR_CREATE_FAILED = 30005;

    /** 文件上传存储失败 */
    public const FILE_UPLOAD_FAILED = 30006;

    /** 导入文件不存在 */
    public const IMPORT_FILE_NOT_EXISTS = 30007;

    /** 文件 MIME 类型错误 */
    public const FILE_MIME_TYPE_ERROR = 30008;

    // ---- 40xxx Excel 处理错误 ----

    /** 输出类型错误 */
    public const OUTPUT_TYPE_ERROR = 40001;

    /** Sheet 不存在 */
    public const SHEET_NOT_EXISTS = 40002;

    /** 列标题不存在 */
    public const COLUMN_HEADER_NOT_EXISTS = 40003;

    // ---- 50xxx 驱动/系统错误 ----

    /** 无效驱动名称 */
    public const DRIVER_INVALID_NAME = 50001;

    /** 驱动类不存在或无效 */
    public const DRIVER_CLASS_INVALID = 50002;

    /** 驱动类未实现接口 */
    public const DRIVER_NOT_IMPLEMENTS = 50003;

    /** 容器解析器未设置 */
    public const CONTAINER_RESOLVER_NOT_SET = 50004;

    /** ExcelInterface 未注册 */
    public const EXCEL_INTERFACE_NOT_REGISTERED = 50005;

    private function __construct()
    {
    }
}
