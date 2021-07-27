<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Currency;
use App\Repository\ExchangeRateRepository;

/**
 * Класс для работы с обменов валют
 */
class ExchangeCurrencyService
{
    /**
     * @var ExchangeRateRepository
     */
    private ExchangeRateRepository $exchangeRateRepository;
    /**
     * @var DateTimeHelper
     */
    private DateTimeHelper $dateTimeHelper;

    public function __construct(ExchangeRateRepository $exchangeRateRepository, DateTimeHelper $dateTimeHelper)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * Переводим из одной валюты в другую
     *
     * @param float    $amount
     * @param Currency $currencyFrom
     * @param Currency $currencyTo
     * @return float
     * @throws \App\Exception\NotFoundEntityException
     */
    public function exchange(float $amount, Currency $currencyFrom, Currency $currencyTo): float
    {
        if (!$currencyFrom->isMain()) {
            $amount = $this->exchangeFromCurrencyToMainCurrency($amount, $currencyFrom);
        }
        if (!$currencyTo->isMain()) {
            $amount = $this->exchangeFromMainCurrencyToCurrency($amount, $currencyTo);
        }

        return $amount;
    }

    /**
     * Обменять валюту на USD
     *
     * @param float    $amount
     * @param Currency $currency
     * @return float
     * @throws \App\Exception\NotFoundEntityException
     */
    public function exchangeFromCurrencyToMainCurrency(float $amount, Currency $currency): float
    {
        $exchangeRate = $this->exchangeRateRepository->findByCurrencyAndDate(
            $currency,
            $this->dateTimeHelper->createCurrentDateWithoutTime()
        );
        return $amount * $exchangeRate->getRate();
    }

    /**
     * Обменять USD на валюту
     *
     * @param float    $amount
     * @param Currency $currency
     * @return float
     * @throws \App\Exception\NotFoundEntityException
     */
    private function exchangeFromMainCurrencyToCurrency(float $amount, Currency $currency): float
    {
        $exchangeRate = $this->exchangeRateRepository->findByCurrencyAndDate(
            $currency,
            $this->dateTimeHelper->createCurrentDateWithoutTime()
        );

        return $amount / $exchangeRate->getRate();
    }
}