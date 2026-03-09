<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

use BusinessG\BaseExcel\Db\ExcelLogInterface;
use BusinessG\BaseExcel\Event\AfterExport;
use BusinessG\BaseExcel\Event\AfterExportOutput;
use BusinessG\BaseExcel\Event\AfterExportSheet;
use BusinessG\BaseExcel\Event\AfterImport;
use BusinessG\BaseExcel\Event\AfterImportSheet;
use BusinessG\BaseExcel\Event\BeforeExport;
use BusinessG\BaseExcel\Event\BeforeExportOutput;
use BusinessG\BaseExcel\Event\BeforeImport;
use BusinessG\BaseExcel\Event\Error;
use BusinessG\BaseExcel\Event\Event;
use BusinessG\BaseExcel\Logger\ExcelLoggerInterface;
use Psr\Container\ContainerInterface;

class ExcelLogDbListener extends AbstractBaseListener
{
    public function __construct(
        ContainerInterface $container,
        ExcelLoggerInterface $excelLogger,
        protected ExcelLogInterface $excelLog
    ) {
        parent::__construct($container, $excelLogger);
    }

    public function process(object $event): void
    {
        /** @var Event $event */
        $enable = $this->excelLog->getConfig()['enable'] ?? true;
        if (!$enable || !$event->config->getIsDbLog()) {
            return;
        }
        parent::process($event);
    }

    public function beforeExport(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function beforeExportExcel(object $event): void
    {
    }

    public function beforeExportData(object $event): void
    {
    }

    public function beforeExportSheet(object $event): void
    {
    }

    public function beforeExportOutput(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterExport(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterExportData(object $event): void
    {
    }

    public function afterExportExcel(object $event): void
    {
    }

    public function afterExportSheet(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterExportOutput(object $event): void
    {
    }

    public function beforeImport(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function beforeImportExcel(object $event): void
    {
    }

    public function beforeImportData(object $event): void
    {
    }

    public function beforeImportSheet(object $event): void
    {
    }

    public function afterImport(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterImportData(object $event): void
    {
    }

    public function afterImportExcel(object $event): void
    {
    }

    public function afterImportSheet(object $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function error(object $event): void
    {
        $this->excelLog->saveLog($event->config, [
            'remark' => $event->exception->getMessage(),
        ]);
    }
}
