<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationException;
use Ramsey\Uuid\Validator\GenericValidator;

class TransferMoneyParamsValidate implements ValidationInterface
{
    /**
     * @inheritDoc
     */
    public function validate(array $data): void
    {
        $clientFrom = $data['client_from'] ?? null;
        if ($clientFrom === null) {
            throw new ValidationException(
                'Для перевода денежных средств, необходимо передать параметр client_from'
            );
        }
        if (!(new GenericValidator())->validate($clientFrom)) {
            throw new ValidationException(
                'Для перевода денежных средств, необходимо передать корректный client_from'
            );
        }
        $clientTo = $data['client_to'] ?? null;
        if ($clientTo === null) {
            throw new ValidationException(
                'Для перевода денежных средств, необходимо передать параметр client_to'
            );
        }
        if (!(new GenericValidator())->validate($clientTo)) {
            throw new ValidationException(
                'Для перевода денежных средств, необходимо передать корректный client_to'
            );
        }
        $currencyCode = $data['currency_code'] ?? null;
        if ($currencyCode === null) {
            throw new ValidationException(
                'Для перевода денежных средств, необходимо передать параметр currency_code'
            );
        }
        if (empty($currencyCode)) {
            throw new ValidationException('Код валюты не должен быть пустым');
        }
        $amount = $data['amount'] ?? null;
        if ($amount === null) {
            throw new ValidationException(
                'Для перевода денежных средств, необходимо передать параметр amount'
            );
        }
        if (!is_numeric($amount)) {
            throw new ValidationException(
                'Денежные средства должны быть корректным числом'
            );
        }
    }
}