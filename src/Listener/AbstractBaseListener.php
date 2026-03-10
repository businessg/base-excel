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
 * 监听器抽象基类，提供事件列表和 process 分发逻辑。
 * 所有事件方法提供空默认实现，子类只需 override 关心的事件。
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

    public function beforeExport(BeforeExport $event): void {}

    public function beforeExportExcel(BeforeExportExcel $event): void {}

    public function beforeExportData(BeforeExportData $event): void {}

    public function beforeExportSheet(BeforeExportSheet $event): void {}

    public function beforeExportOutput(BeforeExportOutput $event): void {}

    public function afterExport(AfterExport $event): void {}

    public function afterExportData(AfterExportData $event): void {}

    public function afterExportExcel(AfterExportExcel $event): void {}

    public function afterExportSheet(AfterExportSheet $event): void {}

    public function afterExportOutput(AfterExportOutput $event): void {}

    public function beforeImport(BeforeImport $event): void {}

    public function beforeImportExcel(BeforeImportExcel $event): void {}

    public function beforeImportData(BeforeImportData $event): void {}

    public function beforeImportSheet(BeforeImportSheet $event): void {}

    public function afterImport(AfterImport $event): void {}

    public function afterImportData(AfterImportData $event): void {}

    public function afterImportExcel(AfterImportExcel $event): void {}

    public function afterImportSheet(AfterImportSheet $event): void {}

    public function error(Error $event): void {}
}
