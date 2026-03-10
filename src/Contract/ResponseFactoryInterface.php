<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

interface ResponseFactoryInterface
{
    /**
     * 创建文件下载响应
     *
     * @return mixed 框架特定的 Response 对象
     */
    public function createDownloadResponse(string $filePath, string $fileName, array $headers = []): mixed;
}
