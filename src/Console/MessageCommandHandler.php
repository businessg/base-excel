<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use BusinessG\BaseExcel\ExcelInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 消息拉取命令逻辑
 */
class MessageCommandHandler
{
    public function __construct(
        protected ExcelInterface $excel,
        protected ProgressDisplay $progressDisplay
    ) {
    }

    public function handle(string $token, int $num, bool $showProgress, OutputInterface $output): int
    {
        $output->writeln('<info>开始获取信息:</info>');

        do {
            $progressRecord = $this->excel->getProgressRecord($token);
            if (!$progressRecord) {
                $output->writeln('<error>未找到进度记录</error>');
                return 1;
            }
            $isEnd = false;
            $messages = $this->excel->popMessageAndIsEnd($token, $num, $isEnd);
            foreach ($messages as $message) {
                $output->writeln($message);
            }
            usleep(500000);
        } while (!$isEnd);

        if ($showProgress) {
            $this->progressDisplay->display($token, $output);
        }

        return 0;
    }
}
