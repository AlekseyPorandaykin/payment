<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\TransferMoneyDto;
use App\Service\TransferMoneyService;
use App\Validation\TransferMoneyParamsValidate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransferMoneyController extends ApiController
{
    /**
     * @var TransferMoneyService
     */
    private TransferMoneyService $transferMoneyService;

    public function __construct(TransferMoneyParamsValidate $validation, TransferMoneyService $transferMoneyService)
    {
        parent::__construct($validation);
        $this->transferMoneyService = $transferMoneyService;
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
        $this->transferMoneyService->transfer(
            new TransferMoneyDto(
                $data['client_from'],
                $data['client_to'],
                $data['currency_code'],
                (float)$data['amount']
            )
        );

        return new JsonResponse(
            [
                'message' => "Перевод денежных средств прошёл успешно",
            ],
            Response::HTTP_CREATED
        );
    }
}