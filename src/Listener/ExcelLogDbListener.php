<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

use BusinessG\BaseExcel\Db\ExcelLogInterface;
use BusinessG\BaseExcel\Event\AfterExport;
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
        $enable = $this->excelLog->getConfig()['enable'] ?? $this->excelLog->getConfig()['enabled'] ?? true;
        if (!$enable || !$event->config->getIsDbLog()) {
            return;
        }
        parent::process($event);
    }

    public function beforeExport(BeforeExport $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function beforeExportOutput(BeforeExportOutput $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterExport(AfterExport $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterExportSheet(AfterExportSheet $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function beforeImport(BeforeImport $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterImport(AfterImport $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function afterImportSheet(AfterImportSheet $event): void
    {
        $this->excelLog->saveLog($event->config);
    }

    public function error(Error $event): void
    {
        $this->excelLog->saveLog($event->config, [
            'remark' => $event->exception->getMessage(),
        ]);
    }
}
