<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\CurrencyDto;
use App\Service\Creator\CurrencyCreator;
use App\Validation\CurrencyParamValidation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CreateCurrencyController extends ApiController
{
    /**
     * @var CurrencyCreator
     */
    private CurrencyCreator $currencyCreator;

    public function __construct(CurrencyParamValidation $validation, CurrencyCreator $currencyCreator)
    {
        parent::__construct($validation);
        $this->currencyCreator = $currencyCreator;
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function handleRequest(array $data): JsonResponse
    {
        $currency = $this->currencyCreator->create(new CurrencyDto($data['code'], $data['name']));
        return new JsonResponse(
            [
                'currency_code' => $currency->getCode(),
                'message'       => 'Валюта создана успешно',
            ],
            Response::HTTP_CREATED
        );
    }
}