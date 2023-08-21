<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Исключение для ошибок бизнес-логики (400е http статусы)
 * Всегда отображаются пользователю
 */
class BusinessErrorException extends Exception
{
    private array $errors;

    public function __construct(string $message = '', array $errors = [], int $code = Response::HTTP_BAD_REQUEST)
    {
        if ($code < 400 || $code > 499) {
            throw new ApplicationErrorException('Invalid 4XX status');
        }

        $this->errors = $errors;
        parent::__construct($message, $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
