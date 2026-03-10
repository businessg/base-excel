<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseConfig;

class ExportConfig extends BaseConfig
{
    public const OUT_PUT_TYPE_UPLOAD = 'upload';
    public const OUT_PUT_TYPE_OUT = 'out';

    public string $outPutType = self::OUT_PUT_TYPE_OUT;
    public array $params = [];

    /** @var Sheet[] */
    public array $sheets = [];

    public function getOutPutType(): string
    {
        return $this->outPutType;
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
            'outPutType' => $this->getOutPutType(),
            'params' => $this->getParams(),
        ];
    }
}
