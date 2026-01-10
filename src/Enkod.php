<?php

namespace Timurrodya\Enkod;

use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use stdClass;
use Timurrodya\Enkod\Contracts\Dtoable;
use Timurrodya\Enkod\Dto\SendEmailDto;
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
     * Создание шаблона сообщения
     *
     * @see https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1create~1/post
     *
     * @param  string  $subject
     * @param  string  $fromEmail
     * @param  string  $fromName
     * @param  string  $html
     * @param  string  $plainText
     * @param  bool  $isTransaction
     * @param  bool  $isActive
     * @param  string|null  $replyToEmail
     * @param  string|null  $replyToName
     * @param  array  $tags
     * @param  object  $utm
     * @param  object  $urlParams
     *
     * @return array|string
     * @throws Exception
     */
    public function messageCreate(
        string $subject,
        string $fromEmail,
        string $fromName,
        string $html,
        string $plainText,
        bool $isTransaction = false,
        bool $isActive = false,
        string $replyToEmail = null,
        string $replyToName = null,
        array $tags = [],
        object $utm = new stdClass,
        object $urlParams = new stdClass,
    ): array|string {
        $data =
            compact('subject', 'fromEmail', 'fromName', 'html', 'plainText', 'isTransaction', 'isActive', 'replyToEmail', 'replyToName', 'tags', 'utm', 'urlParams');

        return $this->request('post', 'message/create/', $data)->json();
    }

    /**
     * Создание мгновенного, запланированного или черновика сообщения
     *
     * @see https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1onetime~1/post
     *
     * @param  object  $message
     * @param  bool  $isDraft
     * @param  object|null  $to
     * @param  Carbon|null  $deliveryDate
     *
     * @return array
     * @throws Exception
     */
    public function messageOnetime(
        object $message,
        bool $isDraft = true,
        object $to = null,
        Carbon $deliveryDate = null,
    ): array {
        $data =
            compact('message', 'isDraft', 'to');
        $data['deliveryDate'] = $deliveryDate?->format('Y-m-d H:i');

        return $this->request('post', 'message/onetime/', $data)->json();
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
}
