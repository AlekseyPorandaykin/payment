<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="exchange_rates",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uniq_currency_date", columns={"currency_code", "date"})},
 *     options={"comment":"Курсы валют"}
 *     )
 */
class ExchangeRate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=36, options={"comment":"Идентификатор курса", "fixed":true})
     */
    private string $guid;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Currency")
     * @ORM\JoinColumn(name="currency_code", referencedColumnName="code")
     */
    private Currency $currency;

    /**
     * Сколько USD готовы предложить за данную валюту
     *
     * @ORM\Column(type="decimal", precision=9, scale=7, options={"comment":"Курс валюты по отношению к USD"})
     */
    private float $rate;

    /**
     * @ORM\Column(type="date", options={"comment":"Дата действия курса"})
     */
    private \DateTime $date;

    /**
     * @ORM\Column(type="datetime", name="created_at", options={"comment":"Дата и время установки курса"})
     */
    private \DateTime $createdAt;

    public function __construct(string $guid, Currency $currency, float $rate, \DateTime $date)
    {
        if ($rate <= 0) {
            throw new \DomainException('Курс валюты должен быть больше 0');
        }
        $this->guid = $guid;
        $this->currency = $currency;
        $this->rate = $rate;
        $this->date = $date;
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
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}