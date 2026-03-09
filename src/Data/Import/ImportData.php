<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Import;

use BusinessG\BaseExcel\Data\BaseObject;

class ImportData extends BaseObject
{
    public string $token = '';
    public array $sheetData = [];
}
