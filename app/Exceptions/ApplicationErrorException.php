<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Исключение для внутренних ошибок приложения (500е http статусы)
 * Отображаются пользователю только в режиме отладки
 */
class ApplicationErrorException extends Exception
{
    public function __construct(string $message = '', int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        if ($code < 500 || $code > 599) {
            throw new ApplicationErrorException('Invalid 5XX status');
        }

        parent::__construct($message, $code);
    }
}
