<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

/**
 * HTTP 配置（路由注册 + 响应格式 + 项目域名）。
 *
 * 框架包（laravel-excel / hyperf-excel）根据此配置决定是否自动注册 Excel HTTP 路由，
 * 以及接口响应的字段命名和项目域名。
 *
 * 路由相关:
 *  - enabled:       是否自动注册路由，默认关闭
 *  - prefix:        路由前缀，如 'api' 则接口为 api/excel/export
 *  - middleware:     应用到路由组的中间件列表，支持字符串别名和完整类名
 *                    如 ['api', \App\Http\Middleware\AuthMiddleware::class]
 *
 * 项目域名:
 *  - domain:        项目域名（含协议），用于 info 接口拼接 templateUrl 的完整地址
 *                   如 'https://example.com'，当 templateUrl 为相对路径时自动拼接
 *
 * 响应格式（response 下）:
 *  - codeField:     响应 JSON 中状态码字段名，默认 'code'
 *  - dataField:     响应 JSON 中数据字段名，默认 'data'
 *  - messageField:  响应 JSON 中消息字段名，默认 'message'
 *  - successCode:   成功时状态码的值，默认 0
 */
final class HttpConfig
{
    public function __construct(
        /** 是否自动注册 Excel HTTP 路由 */
        public readonly bool $enabled = false,
        /** 路由前缀（/excel/* 固定追加在后面） */
        public readonly string $prefix = '',
        /** @var array<string|class-string> 中间件列表，支持别名和类名 */
        public readonly array $middleware = [],
        /** 项目域名（含协议），如 'https://example.com'，为空则不拼接 */
        public readonly string $domain = '',
        /** 响应 JSON 中状态码字段名 */
        public readonly string $codeField = 'code',
        /** 响应 JSON 中数据字段名 */
        public readonly string $dataField = 'data',
        /** 响应 JSON 中消息字段名 */
        public readonly string $messageField = 'message',
        /** 成功时状态码的值 */
        public readonly int|string $successCode = 0,
    ) {
    }

    public static function fromArray(array $raw): self
    {
        $response = $raw['response'] ?? [];
        return new self(
            enabled: $raw['enabled'] ?? false,
            prefix: $raw['prefix'] ?? '',
            middleware: $raw['middleware'] ?? [],
            domain: $raw['domain'] ?? '',
            codeField: $response['codeField'] ?? $raw['codeField'] ?? 'code',
            dataField: $response['dataField'] ?? $raw['dataField'] ?? 'data',
            messageField: $response['messageField'] ?? $raw['messageField'] ?? 'message',
            successCode: $response['successCode'] ?? $raw['successCode'] ?? 0,
        );
    }
}
