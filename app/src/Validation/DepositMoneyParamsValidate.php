<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationException;
use Ramsey\Uuid\Validator\GenericValidator;

class DepositMoneyParamsValidate implements ValidationInterface
{
    public function validate(array $data): void
    {
        $userGuid = $data['user_guid'] ?? null;
        if ($userGuid === null) {
            throw new ValidationException(
                'Для зачисления денежных средств, необходимо передать параметр user_guid'
            );
        }
        if (!(new GenericValidator())->validate($userGuid)) {
            throw new ValidationException(
                'Для зачисления денежных средств, необходимо передать корректный user_guid'
            );
        }
        $amount = $data['amount'] ?? null;
        if ($amount === null) {
            throw new ValidationException(
                'Для зачисления денежных средств, необходимо передать параметр amount'
            );
        }
        if (!is_numeric($amount)) {
            throw new ValidationException(
                'Денежные средства должны быть корректным числом'
            );
        }
    }
}