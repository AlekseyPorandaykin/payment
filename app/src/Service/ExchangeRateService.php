<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ExchangeRateDto;
use App\Entity\ExchangeRate;
use App\Repository\CurrencyRepository;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

/**
 * Класс для работы с курсом валют
 */
class ExchangeRateService
{
    /**
     * @var CurrencyRepository
     */
    private CurrencyRepository $currencyRepository;
    /**
     * @var DateTimeHelper
     */
    private DateTimeHelper $dateTimeHelper;
    /**
     * @var ExchangeRateRepository
     */
    private ExchangeRateRepository $exchangeRateRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(
        CurrencyRepository $currencyRepository,
        DateTimeHelper $dateTimeHelper,
        ExchangeRateRepository $exchangeRateRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->dateTimeHelper = $dateTimeHelper;
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Добавляем курс для валюты на определённый день
     *
     * @param ExchangeRateDto $exchangeRateDto
     * @return ExchangeRate
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addExchangeRateToDay(ExchangeRateDto $exchangeRateDto): ExchangeRate
    {
        $currency = $this->currencyRepository->findByCode($exchangeRateDto->getCurrencyCode());
        if ($currency->isMain()) {
            throw new \DomainException('Нельзя устанавливать ставку у основной валюты');
        }
        $date = $this->dateTimeHelper->createDateWithoutTime($exchangeRateDto->getDateStr());
        $exchangeRate = $this->exchangeRateRepository->getByCurrencyAndDate($currency, $date);
        if ($exchangeRate instanceof ExchangeRate) {
            throw new \DomainException(sprintf(
                "На дату %s уже установлен курс валют %s в '%s",
                $exchangeRate->getDate()->format(DateTimeHelper::DATE_FORMAT_STR),
                $exchangeRate->getCurrency()->getCode(),
                $exchangeRate->getCreatedAt()->format(DateTimeHelper::DATETIME_FORMAT_STR)
            ));
        }
        $exchangeRate = new ExchangeRate(Uuid::uuid4()->toString(), $currency, $exchangeRateDto->getRate(), $date);
        $this->entityManager->persist($exchangeRate);
        $this->entityManager->flush();

        return $exchangeRate;
    }
}