<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\DepositMoneyDto;
use App\Service\WalletManager;
use App\Validation\DepositMoneyParamsValidate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DepositMoneyController extends ApiController
{
    /**
     * @var WalletManager
     */
    private WalletManager $walletManager;

    public function __construct(DepositMoneyParamsValidate $validation, WalletManager $walletManager)
    {
        parent::__construct($validation);
        $this->walletManager = $walletManager;
    }

    /**
     * @param array $data
     * @return JsonResponse
     * @throws \App\Exception\ApplicationException
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function handleRequest(array $data): JsonResponse
    {
        $wallet = $this->walletManager->addAmountToWallet(
            new DepositMoneyDto($data['user_guid'], (float)$data['amount'])
        );
        return new JsonResponse(
            [
                'balance' => $wallet->getBalance(),
                'message' => 'Денежные средства успешно добавлены в кошелёк пользователя',
            ],
            Response::HTTP_CREATED
        );
    }
}