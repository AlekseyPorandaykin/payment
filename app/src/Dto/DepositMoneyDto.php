<?php

declare(strict_types=1);

namespace App\Dto;

class DepositMoneyDto
{
    private string $userGuid;
    private float $amount;

    public function __construct(string $userGuid, float $amount)
    {
        $this->userGuid = $userGuid;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
