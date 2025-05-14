<?php

namespace Timurrodya\Enkod\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    /**
     * @param  int  $code
     * @param  string|null  $message
     * @param  Throwable|null  $previous  ,
     * @param  string|null  $requestId  ID запроса из заголовков
     *
     * @throws Exception
     */
    public function __construct(
        int $code = 0,
        ?string $message = null,
        Throwable $previous = null,
        ?string $requestId = null
    ) {
        $baseMessage = match ($code) {
            400 => 'Ошибка в запросе. Подробная информация в ответе:',
            401 => 'У API ключа нет прав на выполнение этого действия',
            404 => 'Not found',
            500 => 'Что-то пошло не так. Свяжитесь со своим персональным менеджером',
            default => throw new Exception('Неизвестный код ответа'),
        };
        parent::__construct(
            $this->formatErrorMessage($baseMessage, $message, $requestId),
            $code,
            $previous
        );
    }

    protected function formatErrorMessage(
        string $baseMessage,
        ?string $apiMessage,
        ?string $requestId,
    ): string {
        $parts = [$baseMessage];

        if ($requestId) {
            $parts[] = "Request ID: {$requestId}";
        }

        if ($apiMessage) {
            $parts[] = "Сообщение: {$apiMessage}";
        }

        return implode('. ', $parts);
    }

    public function toArray(): array
    {
        return [
            'code'    => $this->code,
            'message' => $this->message,
        ];
    }
}
