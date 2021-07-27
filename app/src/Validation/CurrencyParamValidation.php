<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationException;

class CurrencyParamValidation implements ValidationInterface
{
    public function validate(array $data): void
    {
        $code = $data['code'] ?? null;
        if (!array_key_exists('code', $data) || empty($code)) {
            throw new ValidationException('Для создания валюты необходимо передать параметр code');
        }

        $name = $data['name'] ?? null;
        if ($name === null) {
            throw new ValidationException('Для создания валюты необходимо передать параметр name');
        }
        if (empty($name)) {
            throw new ValidationException('Название валюты не должно быть пустым');
        }
    }
}