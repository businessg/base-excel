<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * 进度查询命令逻辑
 */
class ProgressCommandHandler
{
    public function __construct(
        protected ProgressDisplay $progressDisplay
    ) {
    }

    public function handle(string $token, OutputInterface $output): int
    {
        $this->progressDisplay->display($token, $output);
        return 0;
    }
}
