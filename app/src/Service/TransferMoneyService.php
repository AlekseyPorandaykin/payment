<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TransferMoneyDto;
use App\Entity\Client;
use App\Entity\Currency;
use App\Exception\ApplicationException;
use App\Repository\ClientRepository;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\ApplicationLogicException;

/**
 * Класс для работы с переводами клиентов
 */
class TransferMoneyService
{
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;
    /**
     * @var CurrencyRepository
     */
    private CurrencyRepository $currencyRepository;
    /**
     * @var ExchangeCurrencyService
     */
    private ExchangeCurrencyService $exchangeCurrencyService;
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
        CurrencyRepository $currencyRepository,
        ExchangeCurrencyService $exchangeCurrencyService,
        EntityManagerInterface $entityManager,
        WalletChangeLogger $reportService
    ) {
        $this->clientRepository = $clientRepository;
        $this->currencyRepository = $currencyRepository;
        $this->exchangeCurrencyService = $exchangeCurrencyService;
        $this->entityManager = $entityManager;
        $this->reportService = $reportService;
    }

    /**
     * Переводи денежные средства от клиента отправителя, к клиенту получателю
     *
     * @param TransferMoneyDto $transferMoneyDto
     * @throws ApplicationException
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function transfer(TransferMoneyDto $transferMoneyDto): void
    {
        $clientSender = $this->clientRepository->findByGuid($transferMoneyDto->getClientFrom());
        $clientRecipient = $this->clientRepository->findByGuid($transferMoneyDto->getClientTo());
        if ($clientSender === $clientRecipient) {
            throw new ApplicationLogicException('Нельзя переводить деньги себе');
        }
        $currency = $this->currencyRepository->findByCode($transferMoneyDto->getCurrencyCode());
        if (!in_array(
            $currency,
            [$clientRecipient->getWallet()->getCurrency(), $clientSender->getWallet()->getCurrency()],
            true)
        ) {
            throw new ApplicationLogicException('Переводы можно делать только в валюте получателя или отправителя');
        }
        $this->entityManager->beginTransaction();
        try {
            $this->writeOffAmountFromSender($clientSender, $currency, $transferMoneyDto->getAmount());
            $this->addAmountToRecipient($clientRecipient, $currency, $transferMoneyDto->getAmount());
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw new ApplicationException("Ошибка денежного перевода({$exception->getMessage()})", $exception);
        }
    }

    /**
     * Списываем деньги у клиента отправителя
     *
     * @param Client   $client
     * @param Currency $currency
     * @param float    $amount
     * @throws \App\Exception\NotFoundEntityException
     */
    private function writeOffAmountFromSender(Client $client, Currency $currency, float $amount): void
    {
        $writtenOffAmount = $amount;
        if ($client->getWallet()->getCurrency() !== $currency) {
            //Обмениваем в валюту клиента
            $writtenOffAmount = $this->exchangeCurrencyService->exchange(
                $amount,
                $currency,
                $client->getWallet()->getCurrency()
            );
        }
        $client->getWallet()->writeOffAmount($writtenOffAmount);
        $this->reportService->writeOffAmount($client->getWallet(), $writtenOffAmount);
    }

    /**
     * Зачислим деньги клиенту получателю
     *
     * @param Client   $client
     * @param Currency $currency
     * @param float    $amount
     * @throws \App\Exception\NotFoundEntityException
     */
    private function addAmountToRecipient(Client $client, Currency $currency, float $amount): void
    {
        $depositAmount = $amount;
        if ($client->getWallet()->getCurrency() !== $currency) {
            //Обмениваем в валюту клиента
            $depositAmount = $this->exchangeCurrencyService->exchange(
                $amount,
                $currency,
                $client->getWallet()->getCurrency()
            );
        }
        $client->getWallet()->addAmount($depositAmount);
        $this->reportService->addAmount($client->getWallet(), $depositAmount);
    }
}