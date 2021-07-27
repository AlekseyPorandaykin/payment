<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationException;
use App\Service\DateTimeHelper;
use Ramsey\Uuid\Validator\GenericValidator;

class ReportParamValidate implements ValidationInterface
{
    /**
     * @var DateTimeHelper
     */
    private DateTimeHelper $dateTimeHelper;

    public function __construct(DateTimeHelper $dateTimeHelper)
    {
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $data): void
    {
        $client = $data['client'] ?? null;
        if ($client === null || empty($client)) {
            throw new ValidationException('Для поиска данных установить значение клиент guid');
        }
        if (!(new GenericValidator())->validate($client)) {
            throw new ValidationException(
                'Для поиска отчёта по клиенту необходимо передать корректный клиент guid'
            );
        }
        $dateFrom = $data['date_from'] ?? null;
        if (!empty($dateFrom) && !$this->dateTimeHelper->validateFormatDateString($dateFrom)) {
            throw new ValidationException('Поле date_from должно быть корректной датой');
        }
        $dateTo = $data['date_to'] ?? null;
        if (!empty($dateTo) && !$this->dateTimeHelper->validateFormatDateString($dateTo)) {
            throw new ValidationException('Поле date_to должно быть корректной датой');
        }
    }
}