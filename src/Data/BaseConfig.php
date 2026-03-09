<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data;

abstract class BaseConfig extends BaseObject
{
    public string $serviceName = 'default';
    public string $driverName = '';
    public bool $isAsync = false;
    public bool $isProgress = true;
    public bool $isDbLog = true;
    public array $sheets = [];
    public string $token = '';

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setIsAsync(bool $isAsync): static
    {
        $this->isAsync = $isAsync;
        return $this;
    }

    public function getIsAsync(): bool
    {
        return $this->isAsync;
    }

    public function getIsProgress(): bool
    {
        return $this->isProgress;
    }

    public function getIsDbLog(): bool
    {
        return $this->isDbLog;
    }

    public function getDriverName(): string
    {
        return $this->driverName;
    }

    public function setDriverName(string $driverName): static
    {
        $this->driverName = $driverName;
        return $this;
    }

    public function getSheets(): array
    {
        return $this->sheets;
    }

    public function setSheets($sheets): static
    {
        $this->sheets = $sheets;
        return $this;
    }
}
