<?php

declare(strict_types=1);

namespace App\Dto;

class ExchangeRateDto
{
    private string $currencyCode;
    private float $rate;
    private string $dateStr;

    public function __construct(string $currencyCode, float $rate, string $dateStr)
    {
        $this->currencyCode = $currencyCode;
        $this->rate = $rate;
        $this->dateStr = $dateStr;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @return string
     */
    public function getDateStr(): string
    {
        return $this->dateStr;
    }
}
