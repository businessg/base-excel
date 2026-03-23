<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Import;

use BusinessG\BaseExcel\Data\BaseObject;
use BusinessG\BaseExcel\Exception\ExcelErrorCode;
use BusinessG\BaseExcel\Exception\ExcelException;

class Sheet extends BaseObject
{
    public const SHEET_READ_TYPE_NAME = 'name';
    public const SHEET_READ_TYPE_INDEX = 'index';

    public string $readType = self::SHEET_READ_TYPE_NAME;
    public int $index = 0;
    public string $name = 'sheet1';

    /** @var Column[] */
    public array $columns = [];

    public int $headerIndex = 1;
    public bool $isReturnSheetData = false;
    public bool $skipEmptyRow = true;
    public bool $skipRowIndex = false;
    public bool $isSetHeader = false;
    public mixed $callback = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumnTypes(array $header = []): array
    {
        $columnTypes = [];
        foreach ($this->columns as $column) {
            $columnTypes[$column->title] = $column->type ?: Column::TYPE_STRING;
        }
        $types = array_values($columnTypes);
        if (!empty($header)) {
            $types = array_map(fn ($title) => $columnTypes[$title] ?? Column::TYPE_STRING, $header);
        }
        return $types;
    }

    public function formatSheetDataByHeader($sheetData, $header): array
    {
        return array_map(fn ($n) => $this->formatRowByHeader($n, $header), $sheetData);
    }

    public function formatRowByHeader($row, $header): array
    {
        $data = [];
        if (!empty($this->columns)) {
            $header = array_flip($header);
            foreach ($this->columns as $k => $column) {
                $key = $column->field ?: $column->title;
                if ($header && !isset($header[$column->title])) {
                    throw new ExcelException("The corresponding column header does not exist for [{$column->title}]", ExcelErrorCode::COLUMN_HEADER_NOT_EXISTS);
                }
                $headerKey = $column->title ? ($header[$column->title] ?? $k) : $k;
                $value = $row[$headerKey] ?? null;
                if (!empty($key)) {
                    $data[$key] = $value;
                } else {
                    $data[] = $row[$headerKey] ?? null;
                }
            }
        } else {
            $data = $header ? array_combine($header, $row) : $row;
        }
        return $data;
    }

    public function validateHeader(array $header = []): void
    {
        foreach ($this->columns as $column) {
            if (!in_array($column->title, $header)) {
                throw new ExcelException("The column header does not exist in [{$column->title}]", ExcelErrorCode::COLUMN_HEADER_NOT_EXISTS);
            }
        }
    }
}
