<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\ExcelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommandHandler
{
    public static function getCommandName(): string
    {
        return 'excel:import';
    }

    public static function configureTo(Command $command): void
    {
        $command->setDescription('Run import');
        $command->addArgument('config', InputArgument::REQUIRED, 'The config of import.');
        $command->addArgument('path', InputArgument::REQUIRED, 'The file path of import.');
        $command->addOption('progress', 'g', InputOption::VALUE_NEGATABLE, 'The progress path of import.', true);
        $command->addUsage('excel:import "App\\Excel\\DemoImportConfig" "https://xxx.com/demo.xlsx"');
        $command->addUsage('excel:import "App\\Excel\\DemoImportConfig" "/excel/demo.xlsx"');
        $command->addUsage('excel:import "App\\Excel\\DemoImportConfig" "/excel/demo.xlsx" --no-progress');
    }

    public function __construct(
        protected ExcelInterface $excel,
        protected ProgressDisplay $progressDisplay
    ) {
    }

    /**
     * @return array{token: string, exitCode: int}
     */
    public function handle(string $configClass, string $path, bool $showProgress, OutputInterface $output): array
    {
        $config = new $configClass([]);
        if (!$config instanceof ImportConfig) {
            $output->writeln('<error>Invalid config: expected instance of ' . ImportConfig::class . '</error>');
            return ['token' => '', 'exitCode' => 1];
        }

        if ($path) {
            $config->setPath($path);
        }

        $data = $this->excel->import($config);

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
