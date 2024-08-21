<?php

namespace Timurrodya\Enkod;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Class Enkod
 *
 * @package Timurrodya\Enkod
 */
class Enkod
{
    public PendingRequest $client;

    public function __construct()
    {
        $baseUrl = sprintf(
            "%s/%s",
            rtrim((string) config('enkod.baseurl'), '/'),
            rtrim(config('enkod.version'), '/'),
        );

        $this->client = Http::withHeaders([
            'apiKey' => config('enkod.apiKey'),
            'Accept' => 'application/json',
        ])
            ->baseUrl($baseUrl)
            ->retry(3, 100, fn($exception): bool => $exception instanceof ConnectionException)
            ->acceptJson()
            ->contentType('application/json');
    }

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

        return $this->client->withBody(json_encode($data), 'json')->post('mail')->ok();
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
     */
    public function mails(int $messageId, object $recipients): bool
    {
        $data = [
            "messageId"  => $messageId,
            "recipients" => [$recipients],
        ];

        return $this->client->withBody(json_encode($data), 'json')->post('mails')->ok();
    }
}
