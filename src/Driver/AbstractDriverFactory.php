<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Driver;

use BusinessG\BaseExcel\Exception\InvalidDriverException;
use Psr\Container\ContainerInterface;

/**
 * 框架无关的 DriverFactory 抽象基类，框架只需实现 getConfigValue 和 makeDriver
 */
abstract class AbstractDriverFactory
{
    /**
     * @var DriverInterface[]
     */
    protected array $drivers = [];

    protected array $configs = [];

    /**
     * @throws InvalidDriverException when the driver class not exist or the class is not implemented DriverInterface
     */
    public function __construct(protected ContainerInterface $container)
    {
        $options = $this->getConfigValue('excel.options');
        $this->configs = $this->getConfigValue('excel.drivers', []);

        foreach ($this->configs as $key => $item) {
            $item = array_merge($options ?? [], $item);
            $driverClass = $item['driver'];

            if (!class_exists($driverClass)) {
                throw new InvalidDriverException(sprintf('[Error] class %s is invalid.', $driverClass));
            }

            $driver = $this->makeDriver($driverClass, ['config' => $item, 'name' => $key]);
            if (!$driver instanceof DriverInterface) {
                throw new InvalidDriverException(sprintf('[Error] class %s is not instanceof %s.', $driverClass, DriverInterface::class));
            }

            $this->drivers[$key] = $driver;
        }
    }

    /**
     * 获取配置值
     */
    abstract protected function getConfigValue(string $key, mixed $default = null): mixed;

    /**
     * 通过容器创建 Driver 实例
     */
    abstract protected function makeDriver(string $class, array $params): DriverInterface;

    public function __get(string $name): DriverInterface
    {
        return $this->get($name);
    }

    /**
     * @throws InvalidDriverException when the driver invalid
     */
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
}
