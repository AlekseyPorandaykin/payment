<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Wallet;
use App\Exception\ApplicationException;
use App\Repository\ClientRepository;
use App\Repository\HistoryWalletRepository;
use App\Dto\HistoryWalletDto;

/**
 * Класс для сохранения историей денежных операций
 */
class WalletChangeLogger
{
    /**
     * @var HistoryWalletRepository
     */
    private HistoryWalletRepository $historyWalletRepository;
    /**
     * @var ExchangeCurrencyService
     */
    private ExchangeCurrencyService $exchangeCurrencyService;

    public function __construct(
        HistoryWalletRepository $historyWalletRepository,
        ExchangeCurrencyService $exchangeCurrencyService
    ) {
        $this->historyWalletRepository = $historyWalletRepository;
        $this->exchangeCurrencyService = $exchangeCurrencyService;
    }

    /**
     * @param Wallet $wallet
     * @throws \App\Exception\NotFoundEntityException
     */
    public function createClient(Wallet $wallet): void
    {
        $this->addRecord($wallet, 0, 'Создание нового клиента');
    }

    /**
     * @param Wallet $wallet
     * @param float  $amount
     * @throws \App\Exception\NotFoundEntityException
     */
    public function addAmount(Wallet $wallet, float $amount): void
    {
        $this->addRecord($wallet, $amount, 'Добавление денег в кошелёк');
    }

    /**
     * @param Wallet $wallet
     * @param float  $amount
     * @throws \App\Exception\NotFoundEntityException
     */
    public function writeOffAmount(Wallet $wallet, float $amount): void
    {
        $this->addRecord($wallet, $amount, 'Списание денег в кошельке');
    }

    /**
     * @param Wallet      $wallet
     * @param float|null  $currencyAmount
     * @param string|null $comment
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Doctrine\DBAL\Exception
     * @throws ApplicationException
     */
    public function addRecord(Wallet $wallet, float $currencyAmount = null, string $comment = null): void
    {
        $amount = $currencyAmount;
        if (!$wallet->getCurrency()->isMain() && $amount !== 0) {
            $currencyAmount = $this->exchangeCurrencyService->exchangeFromCurrencyToMainCurrency($amount,
                $wallet->getCurrency());
        }
        if (!$this->historyWalletRepository->addRecord(new HistoryWalletDto($wallet, $amount, $currencyAmount,
            $comment))) {
            throw new ApplicationException("История операции не сохранилась ({$comment})");
        }
    }
}