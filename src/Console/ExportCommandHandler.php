<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use BusinessG\BaseExcel\Data\Export\ExportConfig;
use BusinessG\BaseExcel\ExcelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommandHandler
{
    public static function getCommandName(): string
    {
        return 'excel:export';
    }

    public static function configureTo(Command $command): void
    {
        $command->setDescription('Run export');
        $command->addArgument('config', InputArgument::REQUIRED, 'The config of export.');
        $command->addOption('progress', 'g', InputOption::VALUE_NEGATABLE, 'The progress of export.', true);
        $command->addUsage('excel:export "App\\Excel\\DemoExportConfig"');
        $command->addUsage('excel:export "App\\Excel\\DemoExportConfig" --no-progress');
    }

    public function __construct(
        protected ExcelInterface $excel,
        protected ProgressDisplay $progressDisplay
    ) {
    }

    /**
     * @return array{token: string, exitCode: int}
     */
    public function handle(string $configClass, bool $showProgress, OutputInterface $output): array
    {
        $config = new $configClass([]);
        if (!$config instanceof ExportConfig) {
            $output->writeln('<error>Invalid config: expected instance of ' . ExportConfig::class . '</error>');
            return ['token' => '', 'exitCode' => 1];
        }

        $data = $this->excel->export($config);

        $table = new \Symfony\Component\Console\Helper\Table($output);
        $table->setHeaders(['token']);
        $table->setRows([[ $data->token ]]);
        $table->render();

        if ($showProgress) {
            $this->progressDisplay->display($data->token, $output);
        }

        return ['token' => $data->token, 'exitCode' => 0];
    }
}
