<?php

namespace App\Http\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JResponse
{
    /**
     * @param null $data Данные ответа
     * @param int $status HTTP cтатус ответа
     * @return JsonResponse
     */
    public static function success($data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $status);
    }

    /**
     * @param null $data Данные ответа
     * @return JsonResponse
     */
    public static function create($data = null): JsonResponse
    {
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @param string $message Основное сообщение об ошибке
     * @param array $errors Дополнительные параметры ошибки
     * @param int $status HTTP cтатус ошибки
     * @return JsonResponse
     */
    public static function error(string $message = '', array $errors = [], int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $data = [
            'message' => $message,
            'errors' => $errors
        ];
        return response()->json($data, $status);
    }

    public static function errorFromException(Throwable $e): JsonResponse
    {
        $data = [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ];
        return response()->json($data, $e->getCode());
    }
}
