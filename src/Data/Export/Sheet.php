<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseObject;

class Sheet extends BaseObject
{
    public string $name = 'sheet1';

    /** @var Column[] */
    public array $columns = [];

    public int $count = 0;
    public int $pageSize = 2000;
    public ?SheetStyle $style = null;

    /** @var \Closure|array */
    public \Closure|array $data = [];

    public array $options = [];

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): \Closure|array
    {
        return $this->data;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getHeaders(array $columns): array
    {
        return array_map(fn (Column $col) => $col->title, $columns);
    }

    public function formatRow($row, array $columns): array
    {
        $newRow = [];
        foreach ($columns as $column) {
            $newRow[$column->field] = $row[$column->field] ?? '';
            if (is_callable($column->callback)) {
                $newRow[$column->field] = call_user_func($column->callback, $row);
            }
        }
        return $newRow;
    }

    public function formatList($list, array $columns): array
    {
        return array_map(fn ($item) => $this->formatRow($item, $columns), $list ?? []);
    }
}
