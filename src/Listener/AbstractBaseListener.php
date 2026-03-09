<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;

use BusinessG\BaseExcel\Event\AfterExport;
use BusinessG\BaseExcel\Event\AfterExportData;
use BusinessG\BaseExcel\Event\AfterExportExcel;
use BusinessG\BaseExcel\Event\AfterExportOutput;
use BusinessG\BaseExcel\Event\AfterExportSheet;
use BusinessG\BaseExcel\Event\AfterImport;
use BusinessG\BaseExcel\Event\AfterImportData;
use BusinessG\BaseExcel\Event\AfterImportExcel;
use BusinessG\BaseExcel\Event\AfterImportSheet;
use BusinessG\BaseExcel\Event\BeforeExport;
use BusinessG\BaseExcel\Event\BeforeExportData;
use BusinessG\BaseExcel\Event\BeforeExportExcel;
use BusinessG\BaseExcel\Event\BeforeExportOutput;
use BusinessG\BaseExcel\Event\BeforeExportSheet;
use BusinessG\BaseExcel\Event\BeforeImport;
use BusinessG\BaseExcel\Event\BeforeImportData;
use BusinessG\BaseExcel\Event\BeforeImportExcel;
use BusinessG\BaseExcel\Event\BeforeImportSheet;
use BusinessG\BaseExcel\Event\Error;
use BusinessG\BaseExcel\Logger\ExcelLoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * 监听器抽象基类，提供事件列表和 process 分发逻辑
 */
abstract class AbstractBaseListener
{
    protected ContainerInterface $container;
    protected LoggerInterface $logger;

    public function __construct(ContainerInterface $container, ExcelLoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger->getLogger();
    }

    public function listen(): array
    {
        return [
            BeforeExport::class,
            BeforeExportExcel::class,
            BeforeExportData::class,
            BeforeExportSheet::class,
            BeforeExportOutput::class,

            AfterExport::class,
            AfterExportData::class,
            AfterExportExcel::class,
            AfterExportSheet::class,
            AfterExportOutput::class,

            BeforeImport::class,
            BeforeImportExcel::class,
            BeforeImportData::class,
            BeforeImportSheet::class,

            AfterImport::class,
            AfterImportData::class,
            AfterImportExcel::class,
            AfterImportSheet::class,

            Error::class,
        ];
    }

    protected function getEventClass(object $event): string
    {
        return lcfirst(basename(str_replace('\\', '/', get_class($event))));
    }

    public function process(object $event): void
    {
        $className = $this->getEventClass($event);
        $this->{$className}($event);
    }

    abstract public function beforeExport(object $event): void;

    abstract public function beforeExportExcel(object $event): void;

    abstract public function beforeExportData(object $event): void;

    abstract public function beforeExportSheet(object $event): void;

    abstract public function beforeExportOutput(object $event): void;

    abstract public function afterExport(object $event): void;

    abstract public function afterExportData(object $event): void;

    abstract public function afterExportExcel(object $event): void;

    abstract public function afterExportSheet(object $event): void;

    abstract public function afterExportOutput(object $event): void;

    abstract public function beforeImport(object $event): void;

    abstract public function beforeImportExcel(object $event): void;

    abstract public function beforeImportData(object $event): void;

    abstract public function beforeImportSheet(object $event): void;

    abstract public function afterImport(object $event): void;

    abstract public function afterImportData(object $event): void;

    abstract public function afterImportExcel(object $event): void;

    abstract public function afterImportSheet(object $event): void;

    abstract public function error(object $event): void;
}
