# businessg/base-excel

Excel 同步/异步智能配置导入导出**基础组件**，可独立使用。

## 特性

- 仅依赖 PSR 标准与通用库
- **可独立使用**：内置 xlswriter 驱动
- 支持无限极列头配置
- 支持页码、列头、列样式配置
- 提供 Data DTO、Event、Strategy、Driver 抽象

## 依赖

- PHP >= 8.1
- ext-xlswriter（xlswriter 扩展）
- league/flysystem ^3.0
- overtrue/http ^1.2
- psr/container ^1.0 || ^2.0
- psr/event-dispatcher ^1.0
- ramsey/uuid *

## 使用

### 独立使用

需提供 Container、EventDispatcher、Filesystem，并绑定 `ExportPathStrategyInterface`、`TokenStrategyInterface`。独立使用时仅支持 `OUT_PUT_TYPE_UPLOAD`（写入存储）；流式下载需由扩展实现。

### 事件监听器

组件内置若干领域事件（导入/导出生命周期等）。监听器类名列表由 **`BusinessG\BaseExcel\Config\ListenersConfig`** 表示，并作为 **`ExcelConfig::listeners`** 的一部分；从配置数组构建时使用 `ListenersConfig::fromExcelArray($excel)`（或由 `ExcelConfig::fromArray` 统一解析）。

- **未配置 `listeners`、或值为空数组**：使用 `ListenersConfig` 内置默认（一般为 `ProgressListener`、`ExcelLogDbListener`）。
- **配置了非空 `listeners`**：使用配置中的类名（Laravel 等场景下应为 `AbstractBaseListener` 子类，且可被容器解析）。

`ListenerRegistrar::resolveListeners()` 仍可作为便捷入口，内部等价于 `ListenersConfig::fromExcelArray(...)->classNames`。

自定义监听器请继承 `AbstractBaseListener`，在 `listen()` 中返回要订阅的事件类名数组，并在 `process()` 中处理。

## 目录结构

```
src/
├── Console/           # 命令行抽象 (ProgressDisplay, *CommandHandler)
├── Contract/          # 契约接口 (Arrayable)
├── Data/              # 数据模型 (BaseConfig, ExportConfig, ImportConfig, Sheet, Column...)
├── Driver/            # 驱动接口与抽象类
├── Event/             # 领域事件
├── Exception/
├── Helper/
├── Progress/          # 进度接口与 DTO
├── Queue/             # 队列接口
├── Strategy/          # Token、Path 策略
├── Config/            # ExcelConfig、ListenersConfig 等配置 DTO
├── Listener/          # 领域事件监听器（ListenerRegistrar 委托 ListenersConfig）
└── ExcelInterface.php
```

### Console 命令行

- `ProgressDisplay`：进度条展示，接收 `OutputInterface`
- `ExportCommandHandler` / `ImportCommandHandler` / `ProgressCommandHandler` / `MessageCommandHandler`：命令逻辑，可注入并调用

## License

MIT
