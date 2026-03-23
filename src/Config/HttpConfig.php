<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Config;

use BusinessG\BaseExcel\Data\ResponseContext;
use Closure;

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
 * 接口字段命名风格:
 *  - fieldNaming:   接口返回 JSON 的字段命名风格，'camel'（默认，驼峰）或 'snake'（下划线）
 *
 * 响应格式:
 *  - responseCallback: 响应构建闭包，签名为 function(ResponseContext $context): array
 *                      未配置时使用默认闭包，输出 {'code': $context->code, 'data': $context->data, 'message': $context->message}
 */
final class HttpConfig
{
    public readonly Closure $responseCallback;

    public function __construct(
        /** 是否自动注册 Excel HTTP 路由 */
        public readonly bool $enabled = false,
        /** 路由前缀（/excel/* 固定追加在后面） */
        public readonly string $prefix = '',
        /** @var array<string|class-string> 中间件列表，支持别名和类名 */
        public readonly array $middleware = [],
        /** 项目域名（含协议），如 'https://example.com'，为空则不拼接 */
        public readonly string $domain = '',
        /** 接口返回字段命名风格：'camel'（驼峰）或 'snake'（下划线） */
        public readonly string $fieldNaming = 'camel',
        /** 响应构建闭包，为 null 时使用默认闭包 */
        ?Closure $responseCallback = null,
    ) {
        $this->responseCallback = $responseCallback ?? self::getDefaultResponseCallback();
    }

    public static function getDefaultResponseCallback(): Closure
    {
        return static function (ResponseContext $context): array {
            $resp = ['code' => $context->code];
            if ($context->data !== null) {
                $resp['data'] = $context->data;
            }
            if (!$context->isSuccess || $context->message !== '') {
                $resp['message'] = $context->message;
            }
            return $resp;
        };
    }

    public static function fromArray(array $raw): self
    {
        $response = $raw['response'] ?? [];
        $callback = $response['responseCallback'] ?? null;

        return new self(
            enabled: $raw['enabled'] ?? false,
            prefix: $raw['prefix'] ?? '',
            middleware: $raw['middleware'] ?? [],
            domain: $raw['domain'] ?? '',
            fieldNaming: $raw['fieldNaming'] ?? 'camel',
            responseCallback: $callback instanceof Closure ? $callback : null,
        );
    }
}
