<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use BusinessG\BaseExcel\ExcelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MessageCommandHandler
{
    public static function getCommandName(): string
    {
        return 'excel:message';
    }

    public static function configureTo(Command $command): void
    {
        $command->setDescription('Pop messages');
        $command->addArgument('token', InputArgument::REQUIRED, 'The token of export/import.');
        $command->addOption('num', null, InputOption::VALUE_OPTIONAL, 'Number of messages per batch.', '50');
        $command->addOption('progress', 'g', InputOption::VALUE_NEGATABLE, 'Show progress after messages.', true);
        $command->addUsage('excel:message "token-string"');
        $command->addUsage('excel:message "token-string" --num=100 --no-progress');
    }

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
