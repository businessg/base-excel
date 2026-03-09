<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Strategy\Token;

interface TokenStrategyInterface
{
    public function getToken(): string;
}
