<?php

declare(strict_types=1);

namespace App\Dto;

class TransferMoneyDto
{
    private string $clientFrom;
    private string $clientTo;
    private string $currencyCode;
    private float $amount;

    public function __construct(string $clientFrom, string $clientTo, string $currencyCode, float $amount)
    {
        $this->clientFrom = $clientFrom;
        $this->clientTo = $clientTo;
        $this->currencyCode = $currencyCode;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getClientFrom(): string
    {
        return $this->clientFrom;
    }

    /**
     * @return string
     */
    public function getClientTo(): string
    {
        return $this->clientTo;
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
    public function getAmount(): float
    {
        return $this->amount;
    }
}