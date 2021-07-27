<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DepositMoneyDto;
use App\Entity\Wallet;
use App\Exception\ApplicationException;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Класс для управления кошельком клиента
 */
class WalletManager
{
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var WalletChangeLogger
     */
    private WalletChangeLogger $reportService;

    public function __construct(
        ClientRepository $clientRepository,
        EntityManagerInterface $entityManager,
        WalletChangeLogger $reportService
    ) {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->reportService = $reportService;
    }

    /**
     * Зачисление денежных средств клиенту
     *
     * @param DepositMoneyDto $depositMoneyDto
     * @return Wallet
     * @throws ApplicationException
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addAmountToWallet(DepositMoneyDto $depositMoneyDto): Wallet
    {
        $client = $this->clientRepository->findByGuid($depositMoneyDto->getUserGuid());
        $this->entityManager->beginTransaction();
        try {
            $client->getWallet()->addAmount($depositMoneyDto->getAmount());
            $this->entityManager->flush();
            $this->reportService->addAmount($client->getWallet(), $depositMoneyDto->getAmount());

            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw new ApplicationException(
                "Ошибка зачисления денежных средств({$exception->getMessage()})",
                $exception
            );
        }

        return $client->getWallet();
    }
}