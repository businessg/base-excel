<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data;

use BusinessG\BaseExcel\Contract\Arrayable;

class BaseObject implements Arrayable
{
    /** 接口返回字段命名风格：'camel' 或 'snake'，由框架启动时从 HttpConfig 设置 */
    public static string $fieldNaming = 'camel';

    public function __construct(array $config = [])
    {
        $this->initConfig($config);
    }

    protected function initConfig(array $config = []): void
    {
        foreach ($config as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
    }

    public function toArray(): array
    {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        $publicProperties = [];
        $useSnake = static::$fieldNaming === 'snake';
        foreach ($properties as $property) {
            $value = $property->getValue($this);
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }
            $key = $property->getName();
            $publicProperties[$useSnake ? static::camelToSnake($key) : $key] = $value;
        }
        return $publicProperties;
    }

    public static function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($input)));
    }
}
