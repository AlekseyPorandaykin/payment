<?php

declare(strict_types=1);

namespace App\Service\Creator;

use App\Entity\Wallet;
use App\Service\DateTimeHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\ClientDto;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\CurrencyRepository;
use App\Service\WalletChangeLogger;
use Ramsey\Uuid\Uuid;

/**
 * Создание клиента
 */
class ClientCreator
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
     * @var CurrencyRepository
     */
    private CurrencyRepository $currencyRepository;
    /**
     * @var WalletChangeLogger
     */
    private WalletChangeLogger $reportService;

    public function __construct(
        ClientRepository $clientRepository,
        CurrencyRepository $currencyRepository,
        EntityManagerInterface $entityManager,
        WalletChangeLogger $reportService
    ) {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->currencyRepository = $currencyRepository;
        $this->reportService = $reportService;
    }

    /**
     * Создаем клиента и создаём пустой кошелёк
     *
     * @param ClientDto $clientDto
     * @return Client
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Throwable
     */
    public function create(ClientDto $clientDto): Client
    {
        $client = $this->clientRepository->getByParam($clientDto);
        if ($client instanceof Client) {
            throw new \DomainException(sprintf(
                'Пользователь с такими данными уже был добавлен %s',
                $client->getCreatedAt()->format(DateTimeHelper::DATETIME_FORMAT_STR)
            ));
        }
        $currency = $this->currencyRepository->findByCode($clientDto->getCurrencyCode());
        $this->entityManager->beginTransaction();
        try {
            $client = new Client(Uuid::uuid4()->toString(), $clientDto);
            $wallet = new Wallet($client, $currency);
            $this->entityManager->persist($wallet);
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->reportService->createClient($wallet);

            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $client;
    }
}