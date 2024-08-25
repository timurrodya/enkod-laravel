<?php

namespace Timurrodya\Enkod\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    /**
     * @param  int  $code
     * @param  string|null  $message
     * @param  Throwable|null  $previous
     *
     * @throws Exception
     */
    public function __construct(
        int $code = 0,
        string $message = null,
        Throwable $previous = null,
    ) {
        $developer_message = match ($code) {
            400 => 'Ошибка в запросе. Подробная информация в ответе:',
            401 => 'У API ключа нет прав на выполнение этого действия',
            404 => 'Not found',
            500 => 'Что-то пошло не так. Свяжитесь со своим персональным менеджером',
            default => throw new Exception('Неизвестный код ответа'),
        };
        $message = sprintf('%s %s', $developer_message, $message);
        parent::__construct($message, $code, $previous);
    }
}
