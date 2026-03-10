<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel;

use BusinessG\BaseExcel\Config\ExcelConfig;
use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Driver\DriverFactory;
use BusinessG\BaseExcel\Driver\DriverInterface;
use Psr\Container\ContainerInterface;

class ExcelInvoker
{
    public function __invoke(ContainerInterface $container): DriverInterface
    {
        $configResolver = $container->get(ConfigResolverInterface::class);
        $excelConfig = ExcelConfig::fromArray($configResolver->get('excel', []));
        return $container->get(DriverFactory::class)->get($excelConfig->default);
    }
}
