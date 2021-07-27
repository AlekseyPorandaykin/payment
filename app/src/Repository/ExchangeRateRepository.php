<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Exception\NotFoundEntityException;
use App\Service\DateTimeHelper;

/**
 * Возможно, при запросе курса валют, стоит кешировать данные, т.к. в течение дня они не меняются
 */
class ExchangeRateRepository extends AbstractRepository
{
    /**
     * @param Currency  $currency
     * @param \DateTime $date
     * @return ExchangeRate|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByCurrencyAndDate(Currency $currency, \DateTime $date): ?ExchangeRate
    {
        return $this->entityManager->createQueryBuilder()->select('exchange_rate')
            ->from(ExchangeRate::class, 'exchange_rate')
            ->andWhere('exchange_rate.currency = :currency')
            ->andWhere('exchange_rate.date = :date')
            ->setParameters(compact('currency', 'date'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Currency  $currency
     * @param \DateTime $date
     * @return ExchangeRate
     * @throws NotFoundEntityException
     */
    public function findByCurrencyAndDate(Currency $currency, \DateTime $date): ExchangeRate
    {
        $exchangeRate = $this->getByCurrencyAndDate($currency, $date);
        if ($exchangeRate === null) {
            throw new NotFoundEntityException(sprintf(
                'Не найден курс валюты %s на %s',
                $currency->getCode(),
                $date->format(DateTimeHelper::DATE_FORMAT_STR)
            ));
        }

        return $exchangeRate;
    }
}