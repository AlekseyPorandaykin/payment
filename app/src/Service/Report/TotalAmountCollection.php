<?php

declare(strict_types=1);

namespace App\Service\Report;

/**
 * Коллекция для хранения общей суммы операций по счёту в соответствующих валютах
 */
class TotalAmountCollection
{
    private array $totalSum = [];

    /**
     * Добавим сумму в соответствующей валюте
     *
     * @param string $currencyCode
     * @param float  $amount
     */
    public function increaseAmount(string $currencyCode, float $amount): void
    {
        if (!isset($this->totalSum[$currencyCode])) {
            $this->totalSum[$currencyCode] = 0;
        }
        $this->totalSum[$currencyCode] += $amount;
    }

    public function toArray(): array
    {
        return $this->totalSum;
    }
}