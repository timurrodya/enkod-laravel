<?php

namespace Timurrodya\Enkod;

use Exception;
use InvalidArgumentException;
use Timurrodya\Enkod\Contracts\Dtoable;
use Timurrodya\Enkod\Dto\MessageContentDto;
use Timurrodya\Enkod\Dto\MessageCreateDto;
use Timurrodya\Enkod\Dto\MessageOnetimeDto;
use Timurrodya\Enkod\Dto\SendEmailDto;
use Timurrodya\Enkod\Dto\SmtpBatchEmailDto;
use Timurrodya\Enkod\Dto\SmtpEmailDto;

/**
 * Class Enkod
 *
 * @package Timurrodya\Enkod
 */
class Enkod extends ApiClient
{
    /**
     * Отправка сообщения единственному получателю
     *
     * @see https://openapi.enkod.io/#tag/Emails/paths/~1v1~1mail~1/post
     *
     *
     * @param  SendEmailDto|array{messageId: int, email: string, snippets?: array, attachments?: array}  $data
     *
     * @return bool
     * @throws Exception
     */
    public function mail(SendEmailDto|array $data): bool
    {
        $dto = $this->resolveDto(SendEmailDto::class, $data);

        return $this->request('post', 'mail', $dto->toArray())->ok();
    }

    /**
     * Универсальный метод преобразования в DTO
     *
     * @template T of Dtoable
     * @param  class-string<T>  $dtoClass
     * @param  Dtoable|array  $data
     *
     * @return Dtoable
     */
    protected function resolveDto(string $dtoClass, Dtoable|array $data): Dtoable
    {
        return match (true) {
            $data instanceof $dtoClass => $data,
            is_array($data) => $dtoClass::fromArray($data),
            default => throw new InvalidArgumentException(
                "Invalid input type, expected $dtoClass or array"
            ),
        };
    }

    /**
     * Отправка сообщения нескольким получателям
     *
     * @see https://openapi.enkod.io/#tag/Emails/paths/~1v1~1mails~1/post
     *
     * @param  int  $messageId
     * @param  object  $recipients
     *
     * @return bool
     * @throws Exception
     */
    public function mails(int $messageId, object $recipients): bool
    {
        $data = [
            "messageId"  => $messageId,
            "recipients" => [$recipients],
        ];

        return $this->request('post', 'mails', $data)->ok();
    }

    /**
     * Создание шаблона сообщения для отправки по API
     *
     * Метод создает шаблон email-сообщения в системе Enkod. Созданный шаблон можно использовать
     * для отправки через методы mail() или mails() с полученным messageId.
     *
     *
     * @see https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1create~1/post
     *
     * @param  MessageCreateDto|array{
     *     subject: string,
     *     fromEmail: string,
     *     fromName: string,
     *     html: string,
     *     plainText: string,
     *     isTransaction?: bool,
     *     isActive?: bool,
     *     replyToEmail?: string,
     *     replyToName?: string,
     *     tags?: array,
     *     utm?: object,
     *     urlParams?: object
     * }  $data  Данные для создания шаблона сообщения
     *
     * @return array|string  Массив с данными созданного сообщения (включая messageId) или строка с ошибкой
     * @throws Exception
     */
    public function messageCreate(MessageCreateDto|array $data): array|string
    {
        $dto = $this->resolveDto(\Timurrodya\Enkod\Dto\MessageCreateDto::class, $data);

        return $this->request('post', 'message/create/', $dto->toArray())->json();
    }

    /**
     * Создание мгновенного, запланированного или черновика сообщения
     *
     * @see https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1onetime~1/post
     *
     * @param  MessageOnetimeDto|array{
     *     message: MessageCreateDto|array,
     *     isDraft?: bool,
     *     to?: object|array|null,
     *     deliveryDate?: string|\Carbon\Carbon|null
     * }  $data  Данные сообщения (объект DTO или ассоциативный массив)
     *
     * @return array  Ответ API Enkod в виде массива
     * @throws Exception
     */
    public function messageOnetime(MessageOnetimeDto|array $data): array
    {
        $dto = $this->resolveDto(MessageOnetimeDto::class, $data);

        return $this->request('post', 'message/onetime/', $dto->toArray())->json();
    }

    /**
     * Получение контента сообщения по id
     *
     * @see https://openapi.enkod.io/#/emails/paths/~1v1~1message~1{id}~1content~1/get
     *
     * @param  int  $id  Идентификатор сообщения
     *
     * @return MessageContentDto  Контент сообщения
     * @throws Exception
     */
    public function getMessageContent(int $id): MessageContentDto
    {
        $url = sprintf('message/%d/content/', $id);
        $response = $this->request('get', $url, []);

        return MessageContentDto::fromArray($response->json() ?? []);
    }

    /**
     * Отправка email-сообщения по API для работы с сервисом как с SMTP
     *
     * @see https://openapi.enkod.io/#/emails/paths/~1smtp~1{sendingdomain}~1/post
     *
     * @param  string  $sendingDomain  Домен отправки
     * @param  SmtpEmailDto|array{to: string|array, subject: string, body?: string, html?: string, from?: string, fromName?: string, cc?: string|array, bcc?: string|array, replyTo?: string, attachments?: array}  $data  Данные для отправки
     *
     * @return bool
     * @throws Exception
     */
    public function smtp(string $sendingDomain, SmtpEmailDto|array $data): bool
    {
        $dto = $this->resolveDto(SmtpEmailDto::class, $data);

        // SMTP endpoint находится на корневом уровне без версии, используем относительный путь для выхода из версии
        $url = sprintf('../smtp/%s/', $sendingDomain);

        return $this->request('post', $url, $dto->toArray())->ok();
    }

    /**
     * Отправка batch email-сообщений через v1/smtp/messages
     *
     * @see https://openapi.enkod.io/#/emails/paths/~1v1~1smtp~1messages~1/post
     *
     * @param  SmtpBatchEmailDto[]|array[]  $batch  Массив email-сообщений DTO или массивов
     *
     * @return bool
     * @throws Exception
     */
    public function smtpMessages(array $batch): bool
    {
        $items = array_map(function ($item) {
            if ($item instanceof SmtpBatchEmailDto) {
                return $item->toArray();
            } elseif (is_array($item)) {
                $dto = SmtpBatchEmailDto::fromArray($item);

                return $dto->toArray();
            } else {
                throw new InvalidArgumentException('Each batch item must be array or SmtpBatchEmailDto');
            }
        }, $batch);
        $url = 'v1/smtp/messages';

        return $this->request('post', $url, $items)->ok();
    }
}
