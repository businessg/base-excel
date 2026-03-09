<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Progress;

use BusinessG\BaseExcel\Data\BaseObject;

class ProgressData extends BaseObject
{
    public const PROGRESS_STATUS_AWAIT = 1;
    public const PROGRESS_STATUS_PROCESS = 2;
    public const PROGRESS_STATUS_END = 3;
    public const PROGRESS_STATUS_FAIL = 4;
    public const PROGRESS_STATUS_OUTPUT = 5;
    public const PROGRESS_STATUS_COMPLETE = 6;

    public int $total = 0;
    public int $progress = 0;
    public int $success = 0;
    public int $fail = 0;
    public int $status = self::PROGRESS_STATUS_AWAIT;
    public string $message = '';
}
