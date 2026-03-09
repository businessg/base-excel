<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

use BusinessG\BaseExcel\Event\AfterExport;
use BusinessG\BaseExcel\Event\AfterExportData;
use BusinessG\BaseExcel\Event\AfterExportOutput;
use BusinessG\BaseExcel\Event\AfterExportSheet;
use BusinessG\BaseExcel\Event\AfterImport;
use BusinessG\BaseExcel\Event\AfterImportData;
use BusinessG\BaseExcel\Event\AfterImportSheet;
use BusinessG\BaseExcel\Event\BeforeExport;
use BusinessG\BaseExcel\Event\BeforeExportData;
use BusinessG\BaseExcel\Event\BeforeExportOutput;
use BusinessG\BaseExcel\Event\BeforeExportSheet;
use BusinessG\BaseExcel\Event\BeforeImport;
use BusinessG\BaseExcel\Event\BeforeImportSheet;
use BusinessG\BaseExcel\Event\Error;
use BusinessG\BaseExcel\Event\Event;
use BusinessG\BaseExcel\Logger\ExcelLoggerInterface;
use BusinessG\BaseExcel\Progress\ProgressData;
use BusinessG\BaseExcel\Progress\ProgressInterface;
use Psr\Container\ContainerInterface;

class ProgressListener extends AbstractBaseListener
{
    public function __construct(
        ContainerInterface $container,
        ExcelLoggerInterface $excelLogger,
        protected ProgressInterface $progress
    ) {
        parent::__construct($container, $excelLogger);
    }

    public function process(object $event): void
    {
        /** @var Event $event */
        $enable = $this->progress->getConfig()['enable'] ?? true;
        if (!$enable || !$event->config->getIsProgress()) {
            return;
        }
        parent::process($event);
    }

    public function beforeExport(object $event): void
    {
        $this->progress->initRecord($event->config);
    }

    public function beforeExportExcel(object $event): void
    {
        $this->progress->getRecord($event->config);
    }

    public function beforeExportData(object $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->exportCallbackParam->sheet->name, new ProgressData([
            'total' => $event->exportCallbackParam->totalCount,
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
        ]));
    }

    public function beforeExportSheet(object $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
        ]));
    }

    public function beforeExportOutput(object $event): void
    {
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_OUTPUT,
        ]));
    }

    public function afterExport(object $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $data = $event->data ?? $record->data;
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_COMPLETE,
        ]), $data);
    }

    public function afterExportData(object $event): void
    {
        $success = count($event->data ?? []);
        $this->progress->setSheetProgress($event->config, $event->exportCallbackParam->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
            'success' => $success,
            'progress' => $success,
        ]));
    }

    public function afterExportExcel(object $event): void
    {
    }

    public function afterExportSheet(object $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_END,
        ]));
    }

    public function afterExportOutput(object $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $data = $event->data ?? $record->data;
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_COMPLETE,
        ]), $data);
    }

    public function beforeImport(object $event): void
    {
        $this->progress->initRecord($event->config);
    }

    public function beforeImportExcel(object $event): void
    {
        $this->progress->getRecord($event->config);
    }

    public function beforeImportSheet(object $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
        ]));
    }

    public function beforeImportData(object $event): void
    {
    }

    public function afterImport(object $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $data = $event->data ?? $record->data;
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_COMPLETE,
        ]), $data);
    }

    public function afterImportData(object $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->importCallbackParam->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
            'progress' => 1,
            'success' => $event->exception ? 0 : 1,
            'fail' => $event->exception ? 1 : 0,
        ]));
        if ($event->exception) {
            $this->progress->pushMessage($event->config->getToken(), $event->exception->getMessage());
        }
    }

    public function afterImportExcel(object $event): void
    {
    }

    public function afterImportSheet(object $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_END,
            'total' => $record->sheetListProgress[$event->sheet->name]?->progress ?? 0,
        ]));
    }

    public function error(object $event): void
    {
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_FAIL,
            'message' => $event->exception->getMessage(),
        ]));
        $this->progress->pushMessage($event->config->getToken(), $event->exception->getMessage());
    }
}
