<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wallets", options={"comment":"Таблица <<кошелёк>> клиента"})
 */
class Wallet
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="\App\Entity\Client")
     * @ORM\JoinColumn(name="client_guid", referencedColumnName="guid")
     */
    private Client $client;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Currency")
     * @ORM\JoinColumn(name="currency_code", referencedColumnName="code")
     */
    private Currency $currency;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4, options={"comment":"Денежный остаток"})
     */
    private float $balance;

    /**
     * @ORM\Column(type="datetime", name="created_at", options={"comment":"Дата и время создания кошелька"})
     */
    private \DateTime $createdAt;

    public function __construct(Client $client, Currency $currency, float $balance = 0.00)
    {
        if ($balance < 0) {
            throw new \DomainException('Нельзя открывать счёт с отрицательным балансом');
        }
        $this->balance = $balance;
        $this->client = $client;
        $this->currency = $currency;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param float $amount
     */
    public function addAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \DomainException('Добавить на счёт можно только сумму большую нуля');
        }
        $this->balance += $amount;
    }

    /**
     * @param float $amount
     */
    public function writeOffAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \DomainException('Списать со счёта можно только сумму большую нуля');
        }
        if ($amount > $this->balance) {
            throw new \DomainException('Нельзя списать сумму большую, чем есть на счёте');
        }
        $this->balance -= $amount;
    }
}