<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Db;

/**
 * Repository interface for persisting Excel log records.
 * Framework packages implement this with their ORM (Eloquent, Doctrine, etc.).
 */
interface ExcelLogRepositoryInterface
{
    /**
     * Upsert a log record keyed by 'token'.
     *
     * @param array $data Associative array of column => value
     * @return int Number of affected rows
     */
    public function upsert(array $data): int;
}
