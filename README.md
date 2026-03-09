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
└── ExcelInterface.php
```

### Console 命令行

- `ProgressDisplay`：进度条展示，接收 `OutputInterface`
- `ExportCommandHandler` / `ImportCommandHandler` / `ProgressCommandHandler` / `MessageCommandHandler`：命令逻辑，可注入并调用

## License

MIT
