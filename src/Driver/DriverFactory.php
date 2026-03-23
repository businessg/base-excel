<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Driver;

use BusinessG\BaseExcel\Config\ExcelConfig;
use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Contract\ObjectFactoryInterface;
use BusinessG\BaseExcel\Exception\ExcelErrorCode;
use BusinessG\BaseExcel\Exception\InvalidDriverException;
use Psr\Container\ContainerInterface;

class DriverFactory
{
    /** @var DriverInterface[] */
    protected array $drivers = [];

    protected ExcelConfig $excelConfig;

    public function __construct(
        protected ContainerInterface $container,
        ConfigResolverInterface $configResolver,
        protected ObjectFactoryInterface $objectFactory
    ) {
        $this->excelConfig = ExcelConfig::fromArray($configResolver->get('excel', []));
    }

    public function __get(string $name): DriverInterface
    {
        return $this->get($name);
    }

    public function get(string $name): DriverInterface
    {
        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }
        return $this->drivers[$name];
    }

    public function getConfig(string $name): array
    {
        return $this->excelConfig->getDriverConfig($name);
    }

    public function getDriverNames(): array
    {
        return array_keys($this->excelConfig->drivers);
    }

    protected function createDriver(string $name): DriverInterface
    {
        $item = $this->excelConfig->getDriverConfig($name);
        if (empty($item)) {
            throw new InvalidDriverException(sprintf('[Error] %s is a invalid driver.', $name), ExcelErrorCode::DRIVER_INVALID_NAME);
        }

        $driverClass = $item['class'] ?? $item['driver'] ?? null;

        if (!$driverClass || !class_exists($driverClass)) {
            throw new InvalidDriverException(sprintf('[Error] class %s is invalid.', $driverClass ?? 'null'), ExcelErrorCode::DRIVER_CLASS_INVALID);
        }

        $driver = $this->objectFactory->make($driverClass, ['config' => $item, 'name' => $name]);
        if (!$driver instanceof DriverInterface) {
            throw new InvalidDriverException(sprintf('[Error] class %s is not instanceof %s.', $driverClass, DriverInterface::class), ExcelErrorCode::DRIVER_NOT_IMPLEMENTS);
        }

        return $driver;
    }
}
