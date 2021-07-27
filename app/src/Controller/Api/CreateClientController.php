<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\ClientDto;
use App\Service\Creator\ClientCreator;
use App\Validation\ClientParamsValidation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CreateClientController extends ApiController
{
    /**
     * @var ClientCreator
     */
    private ClientCreator $clientCreator;

    public function __construct(ClientParamsValidation $validation, ClientCreator $clientCreator)
    {
        parent::__construct($validation);
        $this->clientCreator = $clientCreator;
    }

    /**
     * @param array $data
     * @return JsonResponse
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Throwable
     */
    protected function handleRequest(array $data): JsonResponse
    {
        $client = $this->clientCreator->create(
            new ClientDto($data['name'], $data['country'], $data['city'], $data['currency_code'])
        );

        return new JsonResponse(
            [
                'user_guid' => $client->getGuid(),
                'message'   => "Пользователь '{$client->getName()}' создан успешно",
            ],
            Response::HTTP_CREATED
        );
    }
}