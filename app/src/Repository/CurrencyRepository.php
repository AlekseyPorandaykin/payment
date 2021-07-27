<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Currency;
use App\Exception\NotFoundEntityException;

class CurrencyRepository extends AbstractRepository
{
    /**
     * @param string $code
     * @return Currency|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByCode(string $code): ?Currency
    {
        return $this->entityManager->createQueryBuilder()
            ->select('currency')
            ->from(Currency::class, 'currency')
            ->where('currency.code = :code')
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     * @return Currency
     * @throws NotFoundEntityException
     */
    public function findByCode(string $code): Currency
    {
        $currency = $this->getByCode($code);
        if ($currency === null) {
            throw new NotFoundEntityException("Валюта с кодом '{$code}' не найдена");
        }

        return $currency;
    }
}