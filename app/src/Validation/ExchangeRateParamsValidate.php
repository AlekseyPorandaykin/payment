<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationException;
use App\Service\DateTimeHelper;

class ExchangeRateParamsValidate implements ValidationInterface
{
    /**
     * @var DateTimeHelper
     */
    private DateTimeHelper $dateTimeHelper;

    public function __construct(DateTimeHelper $dateTimeHelper)
    {
        $this->dateTimeHelper = $dateTimeHelper;
    }

    public function validate(array $data): void
    {
        $currencyCode = $data['currency_code'] ?? null;
        if ($currencyCode === null) {
            throw new ValidationException('Для заведения курса валют необходимо передать параметр currency_code');
        }
        if (empty($currencyCode)) {
            throw new ValidationException('Код валюты не должен быть пустым');
        }
        $rate = $data['rate'] ?? null;

        if ($rate === null) {
            throw new ValidationException('Для заведения курса валют необходимо передать параметр rate');
        }
        if (!is_numeric($rate)) {
            throw new ValidationException(
                'Курс валюты должен быть корректным числом'
            );
        }
        $date = $data['date'] ?? null;
        if ($date === null) {
            throw new ValidationException('Для заведения курса валют необходимо передать параметр date');
        }
        if (!$this->dateTimeHelper->validateFormatDateString($date)) {
            throw new ValidationException(
                sprintf(
                    "День установки курса валюты дата должна быть корректным в формате '%s'",
                    DateTimeHelper::DATE_FORMAT_STR
                )
            );
        }
    }
}