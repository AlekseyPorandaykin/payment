<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Validation\ValidationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController
{
    private ValidationInterface $validation;

    public function __construct(ValidationInterface $validation)
    {
        $this->validation = $validation;
    }

    public function __invoke(Request $request)
    {
        try {
            $content = \json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $this->validation->validate($content);

            return $this->handleRequest($content);
        } catch (\Throwable $exception) {
            return new JsonResponse(['error' => 'Произошла ошибка: ' . $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    abstract protected function handleRequest(array $data): JsonResponse;
}