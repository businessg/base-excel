<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Contract;

use League\Flysystem\FilesystemOperator;

interface FilesystemResolverInterface
{
    public function getFilesystem(string $disk = 'local'): FilesystemOperator;
}
