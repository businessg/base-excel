<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Strategy\Token;

use BusinessG\BaseExcel\Helper\Helper;

class UuidStrategy implements TokenStrategyInterface
{
    public function getToken(): string
    {
        return Helper::uuid4();
    }
}
