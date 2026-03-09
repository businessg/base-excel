<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseObject;

class Column extends BaseObject
{
    public string $key = '';
    public string $title = '';
    public string $type = '';
    public string $field = '';
    public mixed $callback = null;
    public int $width = 0;
    public int $height = 0;
    public int $col = 0;
    public int $row = 0;
    public int $rowSpan = 0;
    public int $colSpan = 0;
    public ?Style $style = null;
    public ?Style $headerStyle = null;

    /** @var Column[] */
    public array $children = [];

    public bool $hasChildren = false;
    public array $options = [];

    public static function processColumns(array $columns): array
    {
        $maxDepth = static::calculateMaxDepth($columns);
        $result = static::processColumnsRecursive($columns, 0, $maxDepth - 1, 0);
        return [
            $result['leafNodes'],
            $result['fullStructure'],
            $maxDepth,
        ];
    }

    protected static function processColumnsRecursive(array $columns, int $startRow, int $endRow, int $startCol): array
    {
        $leafNodes = [];
        $fullStructure = [];
        $currentCol = $startCol;

        foreach ($columns as $column) {
            $hasChildren = !empty($column->children);
            $rowSpan = $hasChildren ? 1 : ($endRow - $startRow + 1);
            $colSpan = $hasChildren ? static::countLeafColumns($column->children) : 1;

            $columnInfo = new Column([
                'title' => $column->title,
                'field' => $column->field,
                'row' => $startRow,
                'width' => $column->width,
                'height' => $column->height,
                'col' => $currentCol,
                'rowSpan' => $rowSpan,
                'colSpan' => $colSpan,
                'style' => $column->style ?? null,
                'headerStyle' => $column->headerStyle ?? null,
                'hasChildren' => $hasChildren,
            ]);

            $fullStructure[] = $columnInfo;

            if ($hasChildren) {
                $childResult = static::processColumnsRecursive(
                    $column->children,
                    $startRow + 1,
                    $endRow,
                    $currentCol
                );
                $leafNodes = array_merge($leafNodes, $childResult['leafNodes']);
                $fullStructure = array_merge($fullStructure, $childResult['fullStructure']);
                $currentCol += $colSpan;
            } else {
                $leafNodes[] = $columnInfo;
                $currentCol += $colSpan;
            }
        }

        return [
            'leafNodes' => $leafNodes,
            'fullStructure' => $fullStructure,
        ];
    }

    protected static function calculateMaxDepth(array $columns): int
    {
        $maxDepth = 1;
        foreach ($columns as $column) {
            if (!empty($column->children)) {
                $childDepth = static::calculateMaxDepth($column->children) + 1;
                $maxDepth = max($maxDepth, $childDepth);
            }
        }
        return $maxDepth;
    }

    protected static function countLeafColumns(array $columns): int
    {
        $count = 0;
        foreach ($columns as $column) {
            if (empty($column->children)) {
                $count++;
            } else {
                $count += static::countLeafColumns($column->children);
            }
        }
        return $count;
    }
}
