<?php

namespace App\Validation;

use App\Exception\ValidationException;

interface ValidationInterface
{
    /**
     * @param array $data
     * @throws ValidationException
     */
    public function validate(array $data): void;
}