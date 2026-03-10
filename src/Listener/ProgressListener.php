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
use BusinessG\BaseExcel\Event\BeforeExportExcel;
use BusinessG\BaseExcel\Event\BeforeExportOutput;
use BusinessG\BaseExcel\Event\BeforeExportSheet;
use BusinessG\BaseExcel\Event\BeforeImport;
use BusinessG\BaseExcel\Event\BeforeImportExcel;
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
        $enable = $this->progress->getConfig()['enable'] ?? $this->progress->getConfig()['enabled'] ?? true;
        if (!$enable || !$event->config->getIsProgress()) {
            return;
        }
        parent::process($event);
    }

    public function beforeExport(BeforeExport $event): void
    {
        $this->progress->initRecord($event->config);
    }

    public function beforeExportExcel(BeforeExportExcel $event): void
    {
        $this->progress->getRecord($event->config);
    }

    public function beforeExportData(BeforeExportData $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->exportCallbackParam->sheet->name, new ProgressData([
            'total' => $event->exportCallbackParam->totalCount,
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
        ]));
    }

    public function beforeExportSheet(BeforeExportSheet $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
        ]));
    }

    public function beforeExportOutput(BeforeExportOutput $event): void
    {
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_OUTPUT,
        ]));
    }

    public function afterExport(AfterExport $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $data = $event->data ?? $record->data;
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_COMPLETE,
        ]), $data);
    }

    public function afterExportData(AfterExportData $event): void
    {
        $success = count($event->data ?? []);
        $this->progress->setSheetProgress($event->config, $event->exportCallbackParam->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
            'success' => $success,
            'progress' => $success,
        ]));
    }

    public function afterExportSheet(AfterExportSheet $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_END,
        ]));
    }

    public function afterExportOutput(AfterExportOutput $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $data = $event->data ?? $record->data;
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_COMPLETE,
        ]), $data);
    }

    public function beforeImport(BeforeImport $event): void
    {
        $this->progress->initRecord($event->config);
    }

    public function beforeImportExcel(BeforeImportExcel $event): void
    {
        $this->progress->getRecord($event->config);
    }

    public function beforeImportSheet(BeforeImportSheet $event): void
    {
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_PROCESS,
        ]));
    }

    public function afterImport(AfterImport $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $data = $event->data ?? $record->data;
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_COMPLETE,
        ]), $data);
    }

    public function afterImportData(AfterImportData $event): void
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

    public function afterImportSheet(AfterImportSheet $event): void
    {
        $record = $this->progress->getRecord($event->config);
        $this->progress->setSheetProgress($event->config, $event->sheet->name, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_END,
            'total' => $record->sheetListProgress[$event->sheet->name]?->progress ?? 0,
        ]));
    }

    public function error(Error $event): void
    {
        $this->progress->setProgress($event->config, new ProgressData([
            'status' => ProgressData::PROGRESS_STATUS_FAIL,
            'message' => $event->exception->getMessage(),
        ]));
        $this->progress->pushMessage($event->config->getToken(), $event->exception->getMessage());
    }
}
