<?php

declare(strict_types=1);

namespace App\Service\Report;

/**
 * Коллекция для хранения данных отчёта
 */
class RecordCollection
{
    private const LIMIT_RECORDS_ON_PAGE = 100;

    private array $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /**
     * @return array
     */
    public function getAllRecords(): array
    {
        return $this->records;
    }

    /**
     * @return int
     */
    public function getCountRecords(): int
    {
        return count($this->records);
    }

    /**
     * @return array
     */
    public function getColumnNames(): array
    {
        $firstKey = array_key_first($this->records);
        return array_keys($this->records[$firstKey]);
    }

    /**
     * @param int $startPosition
     * @param int $endPosition
     * @return array
     */
    public function getPartOfRecords(int $startPosition = 1, int $endPosition = self::LIMIT_RECORDS_ON_PAGE): array
    {
        $data = [];
        if ($startPosition === 1) {
            $startPosition = array_key_first($this->records);
        }
        $max = $startPosition + $endPosition;
        for ($i = $startPosition; $i < ($max); $i++) {
            if (isset($this->records[$i])) {
                $data[] = $this->records[$i];
            }
        }
        return $data;
    }

    /**
     * @param int $limit
     * @return int
     */
    public function countAllParts(int $limit = self::LIMIT_RECORDS_ON_PAGE): int
    {
        return (int)ceil($this->getCountRecords() / $limit);
    }
}