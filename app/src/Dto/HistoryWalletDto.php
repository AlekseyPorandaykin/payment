<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Client;
use App\Entity\Wallet;

class HistoryWalletDto
{
    private string $guidClient;
    private string $nameClient;
    private float $amount;
    private string $currencyCode;
    private ?float $currencyAmount;
    private ?string $comment;

    public function __construct(
        Wallet $wallet,
        float $amount,
        float $currencyAmount,
        string $comment = null
    ) {
        $this->guidClient = $wallet->getClient()->getGuid();
        $this->nameClient = $wallet->getClient()->getName();
        $this->amount = $amount;
        $this->currencyCode = $wallet->getCurrency()->getCode();
        $this->currencyAmount = $currencyAmount;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getNameClient(): string
    {
        return $this->nameClient;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return float|null
     */
    public function getCurrencyAmount(): ?float
    {
        return $this->currencyAmount;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getGuidClient(): string
    {
        return $this->guidClient;
    }
}
