<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Исключения, связанные с приложением
 */
class ApplicationException extends \Exception
{
    public function __construct($message = "", \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}