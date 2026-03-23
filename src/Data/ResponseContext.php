<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data;

/**
 * 响应上下文对象，作为 responseCallback 闭包的唯一参数。
 *
 * @see \BusinessG\BaseExcel\Config\HttpConfig::$responseCallback
 */
final class ResponseContext
{
    public function __construct(
        /** 是否成功 */
        public readonly bool $isSuccess,
        /** 状态码（成功为 0，失败为五位数错误码） */
        public readonly int|string $code,
        /** 响应数据 */
        public readonly mixed $data,
        /** 消息文本 */
        public readonly string $message,
    ) {
    }
}
