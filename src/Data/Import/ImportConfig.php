<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Import;

use BusinessG\BaseExcel\Data\BaseConfig;

class ImportConfig extends BaseConfig
{
    public bool $isReturnSheetData = false;
    public string $path = '';
    public array $params = [];

    /** @var Sheet[] */
    public array $sheets = [];

    private string $tempPath = '';

    public function getPath(): string
    {
        return $this->path;
    }

    public function getIsAsync(): bool
    {
        return $this->isAsync;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    final public function setTempPath(string $tempPath): void
    {
        $this->tempPath = $tempPath;
    }

    final public function getTempPath(): string
    {
        return $this->tempPath;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function __serialize(): array
    {
        return [
            'serviceName' => $this->getServiceName(),
            'driverName' => $this->getDriverName(),
            'token' => $this->getToken(),
            'isAsync' => $this->getIsAsync(),
            'isProgress' => $this->getIsProgress(),
            'isDbLog' => $this->getIsDbLog(),
            'path' => $this->getPath(),
            'params' => $this->getParams(),
        ];
    }
}
