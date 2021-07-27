<?php

declare(strict_types=1);

namespace App\Dto;

class ClientDto
{
    private string $name;
    private string $country;
    private string $city;
    private string $currencyCode;

    public function __construct(string $name, string $country, string $city, string $currencyCode)
    {
        $this->name = $name;
        $this->country = $country;
        $this->city = $city;
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}