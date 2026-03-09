<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Helper;

use Overtrue\Http\Client;
use Ramsey\Uuid\Uuid;

class Helper
{
    public static function uuid4(): string
    {
        return Uuid::uuid4()->getHex()->toString();
    }

    public static function downloadFile(string $remotePath, string $filePath): string|false
    {
        $response = Client::create([
            'response_type' => 'raw',
        ])->request($remotePath, 'GET', [
            'verify' => false,
            'http_errors' => false,
        ]);

        if (@file_put_contents($filePath, $response->getBody()->getContents())) {
            return $filePath;
        }
        return false;
    }

    public static function getTempDir(): string
    {
        return sys_get_temp_dir();
    }

    public static function getTempFileName(string $dir, string $prefix = ''): false|string
    {
        return tempnam($dir, $prefix);
    }

    public static function isUrl($url): false|int
    {
        return preg_match('/^http[s]?:\/\//', $url);
    }

    public static function getMimeType($filePath): false|string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($filePath);
    }

    public static function deleteFile(string $filePath): bool
    {
        return @unlink($filePath);
    }
}
