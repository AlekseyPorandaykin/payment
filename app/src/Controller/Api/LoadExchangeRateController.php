<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\DateTimeHelper;
use App\Service\ExchangeRateService;
use App\Validation\ExchangeRateParamsValidate;
use App\Dto\ExchangeRateDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoadExchangeRateController extends ApiController
{
    /**
     * @var ExchangeRateService
     */
    private ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateParamsValidate $validation, ExchangeRateService $exchangeRateService)
    {
        parent::__construct($validation);
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * @param array $data
     * @return JsonResponse
     * @throws \App\Exception\NotFoundEntityException
     */
    protected function handleRequest(array $data): JsonResponse
    {
        $exchangeRate = $this->exchangeRateService
            ->addExchangeRateToDay(new ExchangeRateDto($data['currency_code'], (float)$data['rate'], $data['date']));

        return new JsonResponse([
            'message'    => 'Курс валюты успешно добавлен',
            'created_at' => $exchangeRate->getCreatedAt()->format(DateTimeHelper::DATETIME_FORMAT_STR),
        ],
            Response::HTTP_CREATED);
    }
}