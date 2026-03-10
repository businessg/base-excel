<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Driver;

use BusinessG\BaseExcel\Contract\ConfigResolverInterface;
use BusinessG\BaseExcel\Contract\ObjectFactoryInterface;
use BusinessG\BaseExcel\Exception\InvalidDriverException;
use Psr\Container\ContainerInterface;

class DriverFactory
{
    /** @var DriverInterface[] */
    protected array $drivers = [];

    protected array $configs = [];

    public function __construct(
        protected ContainerInterface $container,
        protected ConfigResolverInterface $configResolver,
        protected ObjectFactoryInterface $objectFactory
    ) {
        $options = $configResolver->get('excel.options');
        $this->configs = $configResolver->get('excel.drivers', []);

        foreach ($this->configs as $key => $item) {
            $item = array_merge($options ?? [], $item);
            $driverClass = $item['driver'];

            if (!class_exists($driverClass)) {
                throw new InvalidDriverException(sprintf('[Error] class %s is invalid.', $driverClass));
            }

            $driver = $objectFactory->make($driverClass, ['config' => $item, 'name' => $key]);
            if (!$driver instanceof DriverInterface) {
                throw new InvalidDriverException(sprintf('[Error] class %s is not instanceof %s.', $driverClass, DriverInterface::class));
            }

            $this->drivers[$key] = $driver;
        }
    }

    public function __get(string $name): DriverInterface
    {
        return $this->get($name);
    }

    public function get(string $name): DriverInterface
    {
        $driver = $this->drivers[$name] ?? null;
        if (!$driver instanceof DriverInterface) {
            throw new InvalidDriverException(sprintf('[Error]  %s is a invalid driver.', $name));
        }
        return $driver;
    }

    public function getConfig(string $name): array
    {
        return $this->configs[$name] ?? [];
    }

    public function getDriverNames(): array
    {
        return array_keys($this->configs);
    }
}
