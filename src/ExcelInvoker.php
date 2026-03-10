<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel;

use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Driver\DriverFactory;
use BusinessG\BaseExcel\Driver\DriverInterface;
use Psr\Container\ContainerInterface;

class ExcelInvoker
{
    public function __invoke(ContainerInterface $container): DriverInterface
    {
        $config = $container->get(ConfigResolverInterface::class);
        $name = $config->get('excel.default', 'xlswriter');
        return $container->get(DriverFactory::class)->get($name);
    }
}
