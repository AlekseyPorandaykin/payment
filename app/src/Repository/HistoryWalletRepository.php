<?php

namespace App\Repository;

use App\Entity\HistoryWallet;
use App\Service\DateTimeHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\HistoryWalletDto;
use Ramsey\Uuid\Uuid;

/**
 * Запрашиваем из бд только простые массива, запрещено работать с сущностями, т.к. данных очень много
 * и проект может упасть по памяти
 */
class HistoryWalletRepository extends AbstractRepository
{
    /**
     * @var DateTimeHelper
     */
    private DateTimeHelper $dateTimeHelper;

    public function __construct(EntityManagerInterface $entityManager, DateTimeHelper $dateTimeHelper)
    {
        parent::__construct($entityManager);
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * @param HistoryWalletDto $historyWalletDto
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function addRecord(HistoryWalletDto $historyWalletDto): bool
    {
        $data = [
            'created_at'      => $this->dateTimeHelper->createCurrentDateString(),
            'name_client'     => $historyWalletDto->getNameClient(),
            'guid_client'     => $historyWalletDto->getGuidClient(),
            'amount'          => $historyWalletDto->getAmount(),
            'currency_code'   => $historyWalletDto->getCurrencyCode(),
            'currency_amount' => $historyWalletDto->getCurrencyAmount(),
            'comment'         => $historyWalletDto->getComment(),
            'guid'            => Uuid::uuid4()->toString(),
        ];

        return $this->entityManager->getConnection()->insert('history_wallets', $data) > 0;
    }

    /**
     * @param string         $clientGuid
     * @param \DateTime|null $dateTimeFrom
     * @param \DateTime|null $dateTimeTo
     * @return array
     */
    public function getRecordByParams(
        string $clientGuid,
        \DateTime $dateTimeFrom = null,
        \DateTime $dateTimeTo = null
    ): array {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('history')
            ->from(HistoryWallet::class, 'history')
            ->andWhere('history.clientGuid = :clientGuid')
            ->setParameter('clientGuid', $clientGuid);
        if ($dateTimeFrom instanceof \DateTime) {
            $queryBuilder->andWhere('history.createdAt >= :dateTimeFrom')
                ->setParameter('dateTimeFrom', $dateTimeFrom->format(DateTimeHelper::DATETIME_FORMAT_STR));
        }
        if ($dateTimeTo instanceof \DateTime) {
            $queryBuilder->andWhere('history.createdAt <= :dateTimeTo')
                ->setParameter('dateTimeTo', $dateTimeTo->format(DateTimeHelper::DATETIME_FORMAT_STR));
        }
        $queryBuilder->orderBy('history.createdAt', 'DESC');
        $queryBuilder->getQuery()->getArrayResult();

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
