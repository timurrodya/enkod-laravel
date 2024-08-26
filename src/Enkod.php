<?php

namespace Timurrodya\Enkod;

use Carbon\Carbon;
use Exception;
use stdClass;

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
     * @param  int  $messageId
     * @param  string  $email
     * @param  array|null  $snippets
     * @param  array|null  $attachments
     *
     * @return bool
     * @throws Exception
     */
    public function mail(int $messageId, string $email, ?array $snippets = null, ?array $attachments = null): bool
    {
        $data = [
            "messageId" => $messageId,
            "email"     => $email,
        ];
        if (is_array($snippets) && ! empty($snippets)) {
            $data['snippets'] = $snippets;
        }
        if (is_array($attachments) && ! empty($attachments)) {
            $data['attachments'] = $attachments;
        }

        return $this->request('post', 'mail', $data)->ok();
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
}
