<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressCommandHandler
{
    public static function getCommandName(): string
    {
        return 'excel:progress';
    }

    public static function configureTo(Command $command): void
    {
        $command->setDescription('Show progress');
        $command->addArgument('token', InputArgument::REQUIRED, 'The token of export/import.');
        $command->addUsage('excel:progress "token-string"');
    }

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
