<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationException;

class ClientParamsValidation implements ValidationInterface
{
    private const MIN_NAME_STR_LENGTH = 5;
    private const MIN_COUNTRY_STR_LENGTH = 5;

    /**
     * @inheritDoc
     */
    public function validate(array $data): void
    {
        $name = $data['name'] ?? null;
        if ($name === null) {
            throw new ValidationException('Для создания клиента необходимо передать параметр name');
        }
        if (empty($name)) {
            throw new ValidationException('Имя пользователя не должно быть пустым');
        }
        $country = $data['country'] ?? null;
        if ($country === null) {
            throw new ValidationException('Для создания клиента необходимо передать параметр country');
        }
        if (empty($country)) {
            throw new ValidationException('Название страны не должно быть пустым');
        }
        $city = $data['city'] ?? null;
        if ($city === null) {
            throw new ValidationException('Для создания клиента необходимо передать параметр city');
        }
        if (empty($city)) {
            throw new ValidationException('Название города не должно быть пустым');
        }
        $currencyCode = $data['currency_code'] ?? null;
        if ($currencyCode === null) {
            throw new ValidationException('Для создания кошелька клиента необходимо передать параметр currency_code');
        }
        if (empty($currencyCode)) {
            throw new ValidationException('Код валюты не должен быть пустым');
        }
    }
}