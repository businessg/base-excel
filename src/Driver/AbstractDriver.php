<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Driver;

use BusinessG\BaseExcel\Data\Export\ExportCallbackParam;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\ExportData;
use BusinessG\BaseExcel\Data\Export\Sheet as ExportSheet;
use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\Data\Import\ImportData;
use BusinessG\BaseExcel\Data\Import\ImportRowCallbackParam;
use BusinessG\BaseExcel\Data\Import\Sheet as ImportSheet;
use BusinessG\BaseExcel\Event\AfterExportData;
use BusinessG\BaseExcel\Event\AfterExportOutput;
use BusinessG\BaseExcel\Event\AfterImportData;
use BusinessG\BaseExcel\Event\BeforeExportData;
use BusinessG\BaseExcel\Event\BeforeExportOutput;
use BusinessG\BaseExcel\Event\BeforeImportData;
use BusinessG\BaseExcel\Exception\ExcelException;
use BusinessG\BaseExcel\Helper\Helper;
use BusinessG\BaseExcel\Strategy\Path\ExportPathStrategyInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class AbstractDriver implements DriverInterface
{
    public function __construct(
        protected ContainerInterface $container,
        protected array $config,
        protected string $name,
        protected EventDispatcherInterface $event,
        protected FilesystemOperator $filesystem
    ) {
    }

    public function export(ExportConfig $config): ExportData
    {
        $exportData = new ExportData(['token' => $config->getToken()]);
        $filePath = $this->getTempFileName();
        $path = $this->exportExcel($config, $filePath);

        $this->event->dispatch(new BeforeExportOutput($config, $this));
        $exportData->response = $this->exportOutPut($config, $path);
        $this->event->dispatch(new AfterExportOutput($config, $this, $exportData));

        return $exportData;
    }

    public function import(ImportConfig $config): ImportData
    {
        $importData = new ImportData(['token' => $config->getToken()]);
        $config->setTempPath($this->fileToTemp($config->getPath()));
        $importData->sheetData = $this->importExcel($config);
        Helper::deleteFile($config->getTempPath());
        return $importData;
    }

    protected function fileToTemp(string $path): string
    {
        $filePath = $this->getTempFileName();

        if (!Helper::isUrl($path)) {
            if (!is_file($path)) {
                throw new ExcelException(sprintf('File not exists[%s]', $path));
            }
            if (!copy($path, $filePath)) {
                throw new ExcelException('File copy error');
            }
        } else {
            if (!Helper::downloadFile($path, $filePath)) {
                throw new ExcelException('File download error');
            }
        }
        return $filePath;
    }

    public function getTempFileName(): string
    {
        $filePath = Helper::getTempFileName($this->getTempDir(), 'ex_');
        if (!$filePath) {
            throw new ExcelException('Failed to build temporary file');
        }
        return $filePath;
    }

    public function getTempDir(): string
    {
        $dir = ($this->config['temp_dir'] ?? null) ?: Helper::getTempDir() . DIRECTORY_SEPARATOR . $this->getTempDirSuffix();
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new ExcelException('Failed to build temporary directory: ' . $dir);
            }
        }
        return $dir;
    }

    /**
     * 临时目录子路径后缀，框架 Driver 可重写（如 laravel-excel、hyperf-excel）
     */
    protected function getTempDirSuffix(): string
    {
        return 'base-excel';
    }

    protected function exportDataCallback(
        callable $callback,
        ExportConfig $config,
        ExportSheet $sheet,
        int $page,
        int $pageSize,
        ?int $totalCount
    ): mixed {
        $exportCallbackParam = new ExportCallbackParam([
            'driver' => $this,
            'config' => $config,
            'sheet' => $sheet,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalCount' => $totalCount,
        ]);

        $this->event->dispatch(new BeforeExportData($config, $this, $exportCallbackParam));
        $result = call_user_func($callback, $exportCallbackParam);
        $this->event->dispatch(new AfterExportData($config, $this, $exportCallbackParam, $result ?? []));

        return $result;
    }

    protected function exportSheetData(
        callable $writeDataFun,
        ExportSheet $sheet,
        ExportConfig $config,
        array $columns
    ): void {
        $totalCount = $sheet->getCount();
        $pageSize = $sheet->getPageSize();
        $data = $sheet->getData();
        $isCallback = is_callable($data);
        $page = 1;
        $pageNum = (int) ceil($totalCount / $pageSize);

        do {
            $list = $dataCallback = $data;

            if (!$isCallback) {
                $totalCount = 0;
                $dataCallback = function () use (&$totalCount, $list) {
                    return $list;
                };
            }

            $list = $this->exportDataCallback($dataCallback, $config, $sheet, $page, min($totalCount, $pageSize), $totalCount);
            $listCount = count($list ?? []);

            if ($list) {
                $writeDataFun($sheet->formatList($list, $columns));
            }

            $isEnd = !$isCallback || $totalCount <= 0 || $totalCount <= $pageSize || ($listCount < $pageSize || $pageNum <= $page);
            $page++;
        } while (!$isEnd);
    }

    protected function importRowCallback(
        callable $callback,
        ImportConfig $config,
        ImportSheet $sheet,
        array $row,
        int $rowIndex
    ): mixed {
        $importRowCallbackParam = new ImportRowCallbackParam([
            'driver' => $this,
            'sheet' => $sheet,
            'config' => $config,
            'row' => $row,
            'rowIndex' => $rowIndex,
        ]);

        $this->event->dispatch(new BeforeImportData($config, $this, $importRowCallbackParam));
        $exception = null;
        try {
            $result = call_user_func($callback, $importRowCallbackParam);
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }
        $this->event->dispatch(new AfterImportData($config, $this, $importRowCallbackParam, $exception));

        return $result ?? null;
    }

    protected function buildExportPath(ExportConfig $config): string
    {
        $strategy = $this->container->get(ExportPathStrategyInterface::class);
        $rootDir = $this->config['exportDir'] ?? $this->config['export']['rootDir'] ?? null;
        return implode(DIRECTORY_SEPARATOR, array_filter([
            $rootDir,
            $strategy->getPath($config),
        ]));
    }

    /**
     * 导出文件输出 - 子类需实现此方法
     * UPLOAD: 返回存储路径字符串
     * OUT: 返回 Response 对象
     */
    abstract protected function exportOutPut(ExportConfig $config, string $filePath): mixed;

    /**
     * 上传文件到存储 - 供 exportOutPut 的 UPLOAD 分支复用
     */
    protected function uploadToStorage(string $filePath, string $path): string
    {
        $this->filesystem->writeStream($path, fopen($filePath, 'r+'));
        $this->deleteFile($filePath);
        if (!$this->filesystem->fileExists($path)) {
            throw new ExcelException('File upload failed');
        }
        return $path;
    }

    protected function deleteFile(string $filePath): void
    {
        if (file_exists($filePath)) {
            Helper::deleteFile($filePath);
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    abstract public function exportExcel(ExportConfig $config, string $filePath): string;

    abstract public function importExcel(ImportConfig $config): ?array;
}
