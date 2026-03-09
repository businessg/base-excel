# businessg/base-excel

Excel 同步/异步智能配置导入导出**基础组件**，框架无关。

为 [businessg/hyperf-excel](https://github.com/businessg/hyperf-excel) 和 [businessg/laravel-excel](https://github.com/businessg/laravel-excel) 提供共享的数据模型、事件、策略、驱动接口等。

## 特性

- 框架无关，仅依赖 PSR 标准与通用库
- 支持无限极列头配置
- 支持页码、列头、列样式配置
- 提供 Data DTO、Event、Strategy、Driver 抽象

## 依赖

- PHP >= 8.1
- league/flysystem ^3.0
- overtrue/http ^1.2
- psr/container ^1.0 || ^2.0
- psr/event-dispatcher ^1.0
- ramsey/uuid *

## 使用

本包作为基础库，通常通过框架适配包使用：

- **Hyperf**: `businessg/hyperf-excel`
- **Laravel**: `businessg/laravel-excel`

## 目录结构

```
src/
├── Contract/          # 契约接口 (Arrayable)
├── Data/              # 数据模型 (BaseConfig, ExportConfig, ImportConfig, Sheet, Column...)
├── Driver/            # 驱动接口与抽象类
├── Event/             # 领域事件
├── Exception/
├── Helper/
├── Progress/          # 进度接口与 DTO
├── Queue/             # 队列接口
├── Strategy/          # Token、Path 策略
└── ExcelInterface.php
```

## License

MIT
