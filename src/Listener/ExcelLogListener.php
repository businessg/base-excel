<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Listener;


class ExcelLogListener extends AbstractBaseListener
{
    public function beforeExport(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeExportExcel(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeExportData(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeExportSheet(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeExportOutput(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterExport(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterExportData(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterExportExcel(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterExportSheet(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterExportOutput(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeImport(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeImportExcel(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeImportData(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function beforeImportSheet(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterImport(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterImportData(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterImportExcel(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function afterImportSheet(object $event): void
    {
        $this->logger->info(sprintf('event:%s,token:%s', $this->getEventClass($event), $event->config->getToken()), ['config' => $event->config]);
    }

    public function error(object $event): void
    {
        $this->logger->error(sprintf('event:%s,token:%s,error:%s', $this->getEventClass($event), $event->config->getToken(), $event->exception->getMessage()), [
            'config' => $event->config,
            'exception' => $event->exception,
        ]);
    }
}
