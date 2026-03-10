<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Driver;

use BusinessG\BaseExcel\Contract\DeferInterface;
use BusinessG\BaseExcel\Contract\FilesystemResolverInterface;
use BusinessG\BaseExcel\Contract\ResponseFactoryInterface;
use BusinessG\BaseExcel\Data\Export\Column;
use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\Data\Export\SheetStyle;
use BusinessG\BaseExcel\Data\Export\Sheet as ExportSheet;
use BusinessG\BaseExcel\Data\Export\Style;
use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\Data\Import\Sheet as ImportSheet;
use BusinessG\BaseExcel\Event\AfterExportExcel;
use BusinessG\BaseExcel\Event\AfterExportSheet;
use BusinessG\BaseExcel\Event\AfterImportExcel;
use BusinessG\BaseExcel\Event\AfterImportSheet;
use BusinessG\BaseExcel\Event\BeforeExportExcel;
use BusinessG\BaseExcel\Event\BeforeExportSheet;
use BusinessG\BaseExcel\Event\BeforeImportExcel;
use BusinessG\BaseExcel\Event\BeforeImportSheet;
use BusinessG\BaseExcel\Exception\ExcelException;
use BusinessG\BaseExcel\Helper\Helper;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Vtiful\Kernel\Excel;

class XlsWriterDriver extends AbstractDriver
{
    protected ResponseFactoryInterface $responseFactory;
    protected DeferInterface $defer;

    public function __construct(
        ContainerInterface $container,
        array $config,
        string $name,
        EventDispatcherInterface $event,
        FilesystemResolverInterface $filesystemResolver,
        ResponseFactoryInterface $responseFactory,
        DeferInterface $defer
    ) {
        $disk = $config['disk'] ?? $config['filesystem']['storage'] ?? 'local';
        $filesystem = $filesystemResolver->getFilesystem($disk);
        parent::__construct($container, $config, $name, $event, $filesystem);
        $this->responseFactory = $responseFactory;
        $this->defer = $defer;
    }

    public function exportExcel(ExportConfig $config, string $filePath): string
    {
        $excel = new Excel([
            'path' => dirname($filePath),
        ]);

        $this->event->dispatch(new BeforeExportExcel($config, $this));

        foreach (array_values($config->getSheets()) as $index => $sheet) {
            $this->exportSheet($excel, $sheet, $config, $index, $filePath);
        }

        $excel->output();
        $excel->close();
        $this->event->dispatch(new AfterExportExcel($config, $this));

        return $filePath;
    }

    public function importExcel(ImportConfig $config): ?array
    {
        $excel = new Excel([
            'path' => $this->getTempDir(),
        ]);

        $filePath = $config->getTempPath();
        $fileName = basename($filePath);

        $this->checkFile($filePath);

        $sheets = $config->getSheets();
        $excel->openFile($fileName);

        $sheetList = $excel->sheetList();

        $this->event->dispatch(new BeforeImportExcel($config, $this));

        $sheets = array_map(function ($sheet) use ($sheetList) {
            if ($sheet->readType == ImportSheet::SHEET_READ_TYPE_INDEX) {
                $sheetName = $sheetList[$sheet->index];
                $sheet->name = $sheetName;
            }
            if (!in_array($sheet->name, $sheetList)) {
                throw new ExcelException("sheet {$sheet->name} not exist");
            }
            return $sheet;
        }, $sheets);

        $sheetData = [];
        foreach ($sheets as $sheet) {
            $sheetData[$sheet->name] = $this->importSheet($excel, $sheet, $config);
        }

        $excel->close();

        $this->event->dispatch(new AfterImportExcel($config, $this));

        return $sheetData;
    }

    protected function exportOutPut(ExportConfig $config, string $filePath): mixed
    {
        $path = $this->buildExportPath($config);

        switch ($config->outPutType) {
            case ExportConfig::OUT_PUT_TYPE_UPLOAD:
                return $this->uploadToStorage($filePath, $path);

            case ExportConfig::OUT_PUT_TYPE_OUT:
                return $this->exportOutPutStream($config, $filePath, $path);

            default:
                throw new ExcelException('outPutType error');
        }
    }

    protected function exportOutPutStream(ExportConfig $config, string $filePath, string $path): mixed
    {
        $fileName = basename($path);
        $headers = Helper::getExportResponseHeaders($fileName, $filePath);
        return $this->responseFactory->createDownloadResponse($filePath, $fileName, $headers);
    }

    protected function deleteFile(string $filePath): void
    {
        $this->defer->defer(function () use ($filePath) {
            if (file_exists($filePath)) {
                Helper::deleteFile($filePath);
            }
        });
    }

    protected function getTempDirSuffix(): string
    {
        return $this->config['temp_dir_suffix'] ?? 'base-excel';
    }

    protected function exportSheet(Excel $excel, ExportSheet $sheet, ExportConfig $config, int $sheetIndex, string $filePath): void
    {
        $sheetName = $sheet->getName();
        if ($sheetIndex > 0) {
            $excel->addSheet($sheetName);
        } else {
            $excel->fileName(basename($filePath), $sheetName);
        }

        $this->event->dispatch(new BeforeExportSheet($config, $this, $sheet));

        if (!empty($sheet->style)) {
            $this->exportSheetStyle($excel, $sheet->style);
        }

        [$columns, $headers, $maxDepth] = Column::processColumns($sheet->getColumns());

        $this->exportSheetHeader($excel, $headers, $maxDepth);

        $this->exportSheetData(function ($data) use ($excel) {
            $excel->data($data);
        }, $sheet, $config, $columns);

        $this->event->dispatch(new AfterExportSheet($config, $this, $sheet));
    }

    protected function exportSheetStyle(Excel $excel, SheetStyle $style): void
    {
        if ($style->gridline !== null) {
            $excel->gridline($style->gridline);
        }

        if ($style->zoom !== null) {
            $excel->zoom($style->zoom);
        }

        if ($style->hide) {
            $excel->setCurrentSheetHide();
        }
        if ($style->isFirst) {
            $excel->setCurrentSheetIsFirst();
        }
    }

    /** @param Column[] $columns */
    protected function exportSheetHeader(Excel $excel, array $columns, int $maxDepth): void
    {
        foreach ($columns as $column) {
            $colStr = Excel::stringFromColumnIndex($column->col);
            $rowIndex = $column->row + 1;
            $endStr = Excel::stringFromColumnIndex($column->col + $column->colSpan - 1);
            $endRowIndex = $rowIndex + $column->rowSpan - 1;
            $range = "{$colStr}{$rowIndex}:{$endStr}{$endRowIndex}";

            $excel->mergeCells($range, $column->title, !empty($column->headerStyle) ? $this->styleToResource($excel, $column->headerStyle) : null);

            if ($column->height > 0) {
                $excel->setRow($range, $column->height);
            }
            $defaultWidth = 5 * mb_strlen($column->title, 'utf-8');
            $excel->setColumn($range, $column->width > 0 ? $column->width : $defaultWidth, !empty($column->style) ? $this->styleToResource($excel, $column->style) : null);
        }
        $excel->setCurrentLine($maxDepth);
    }

    protected function importSheet(Excel $excel, ImportSheet $sheet, ImportConfig $config): ?array
    {
        $sheetName = $sheet->name;

        $this->event->dispatch(new BeforeImportSheet($config, $this, $sheet));

        $excel->openSheet($sheetName);

        $header = [];
        $sheetData = [];

        if ($sheet->headerIndex > 0) {
            if ($sheet->headerIndex > 1) {
                $excel->setSkipRows($sheet->headerIndex - 1);
            }
            $header = $excel->nextRow();
            $sheet->validateHeader($header);
        }

        $columnTypes = $sheet->getColumnTypes($header ?? []);

        if ($sheet->callback || $header) {
            $rowIndex = 0;
            if ($config->isReturnSheetData) {
                $excel->setType($columnTypes);
                $sheetData = $excel->getSheetData();
                if ($sheet->isSetHeader) {
                    $sheetData = $sheet->formatSheetDataByHeader($sheetData, $header);
                }
            } else {
                while (null !== $row = $excel->nextRow($columnTypes)) {
                    $this->rowCallback($config, $sheet, $row, $header, ++$rowIndex);
                }
            }
        }

        $this->event->dispatch(new AfterImportSheet($config, $this, $sheet));

        return $sheetData;
    }

    protected function rowCallback(ImportConfig $config, ImportSheet $sheet, $row, $header = null, int $rowIndex = 0): void
    {
        if ($header) {
            $row = $sheet->formatRowByHeader($row, $header);
        }
        if (is_callable($sheet->callback)) {
            $this->importRowCallback($sheet->callback, $config, $sheet, $row, $rowIndex);
        }
    }

    protected function checkFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new ExcelException('File does not exist');
        }
        $mimeType = Helper::getMimeType($filePath);
        if (!in_array($mimeType, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/octet-stream',
        ])) {
            throw new ExcelException('File mime type error');
        }
    }

    protected function styleToResource(Excel $excel, Style $style)
    {
        $format = new \Vtiful\Kernel\Format($excel->getHandle());

        if (!empty($style->align)) {
            $format->align(...$style->align);
        }

        if ($style->bold) {
            $format->bold();
        }

        if (!empty($style->font)) {
            $format->font($style->font);
        }

        if ($style->italic) {
            $format->italic();
        }

        if ($style->wrap) {
            $format->wrap();
        }

        if ($style->underline > 0) {
            $format->underline($style->underline);
        }

        if ($style->backgroundColor && $style->backgroundStyle) {
            $format->background($style->backgroundColor, $style->backgroundStyle > 0 ? $style->backgroundStyle : Style::PATTERN_SOLID);
        }

        if ($style->fontSize > 0) {
            $format->fontSize($style->fontSize);
        }

        if ($style->fontColor) {
            $format->fontColor($style->fontColor);
        }

        if ($style->strikeout) {
            $format->strikeout();
        }

        return $format->toResource();
    }
}
