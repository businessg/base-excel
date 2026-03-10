<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Helper;

use BusinessG\BaseExcel\Driver\DriverFactory;
use Overtrue\Http\Client;
use Ramsey\Uuid\Uuid;

class Helper
{
    public static function uuid4(): string
    {
        return Uuid::uuid4()->getHex()->toString();
    }

    public static function downloadFile(string $remotePath, string $filePath, bool $verifySsl = true): string|false
    {
        $response = Client::create([
            'response_type' => 'raw',
        ])->request($remotePath, 'GET', [
            'verify' => $verifySsl,
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
     * 导出文件流式下载的响应头，供框架 Driver 复用
     *
     * @param string $fileName 文件名
     * @param string $filePath 文件路径（用于 Content-Length）
     * @return array<string, string>
     */
    public static function getExportResponseHeaders(string $fileName, string $filePath): array
    {
        return [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . rawurlencode($fileName) . '"',
            'Content-Length' => (string) filesize($filePath),
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate, max-age=0',
            'Pragma' => 'public',
        ];
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

    /**
     * 从 DriverFactory 获取待清理的临时目录列表（去重）
     *
     * @return array<string> 目录路径列表
     */
    public static function getDirectoriesToClean(DriverFactory $driverFactory): array
    {
        $dirs = [];
        foreach ($driverFactory->getDriverNames() as $key) {
            try {
                $driver = $driverFactory->get($key);
                $dir = $driver->getTempDir();
                if ($dir && is_dir($dir) && !in_array($dir, $dirs)) {
                    $dirs[] = $dir;
                }
            } catch (\Throwable) {
                continue;
            }
        }
        return $dirs;
    }
}
