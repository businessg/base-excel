<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

use BusinessG\BaseExcel\Data\BaseObject;

class ProgressRecord extends BaseObject
{
    /** @var ProgressData[]|null */
    public ?array $sheetListProgress = [];

    public ?ProgressData $progress = null;
    public mixed $data = null;

    public function getProgressBySheet(string $sheetName): ProgressData
    {
        return $this->sheetListProgress[$sheetName] ?? new ProgressData();
    }

    public function setProgressBySheet(string $sheetName, ProgressData $progress): static
    {
        $this->sheetListProgress[$sheetName] = $progress;
        return $this;
    }
}
