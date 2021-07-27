<?php

declare(strict_types=1);

namespace App\Service\Creator;

use App\Service\DateTimeHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\CurrencyDto;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;

/**
 * Заведение новой валюты
 */
class CurrencyCreator
{
    /**
     * @var CurrencyRepository
     */
    private CurrencyRepository $currencyRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(CurrencyRepository $currencyRepository, EntityManagerInterface $entityManager)
    {
        $this->currencyRepository = $currencyRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param CurrencyDto $currencyDto
     * @return Currency
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(CurrencyDto $currencyDto): Currency
    {
        $currency = $this->currencyRepository->getByCode($currencyDto->getCode());
        if ($currency instanceof Currency) {
            throw new \DomainException(sprintf(
                'Валюта с такими данными уже была добавлена %s',
                $currency->getCreatedAt()->format(DateTimeHelper::DATETIME_FORMAT_STR)
            ));
        }

        $currency = new Currency($currencyDto);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }
}