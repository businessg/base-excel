<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Console;

use BusinessG\BaseExcel\Data\Import\ImportConfig;
use BusinessG\BaseExcel\ExcelInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 导入命令逻辑
 */
class ImportCommandHandler
{
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
