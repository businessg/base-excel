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

    /**
     * 清理临时目录中超过指定时间的文件
     *
     * @param string $directory 目录路径
     * @param int $maxAgeSeconds 最大存活时间（秒），默认 1800
     * @return array 已删除的文件路径列表
     */
    public static function cleanTempDirectory(string $directory, int $maxAgeSeconds = 1800): array
    {
        $deletedFiles = [];
        $currentTime = time();

        $files = @scandir($directory);
        if ($files === false) {
            return $deletedFiles;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_file($filePath)) {
                $fileTime = filemtime($filePath);
                $ageSeconds = $currentTime - $fileTime;

                if ($ageSeconds > $maxAgeSeconds && self::deleteFile($filePath)) {
                    $deletedFiles[] = $filePath;
                }
            }
        }

        return $deletedFiles;
    }
}
