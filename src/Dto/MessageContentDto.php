<?php

namespace Timurrodya\Enkod\Dto;

use InvalidArgumentException;
use stdClass;
use Timurrodya\Enkod\Contracts\Dtoable;

/**
 * DTO ответа GET /v1/message/{id}/content/ — контент сообщения.
 */
class MessageContentDto implements Dtoable
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $subject = '',
        public readonly string $fromEmail = '',
        public readonly string $fromName = '',
        public readonly string $html = '',
        public readonly string $plainText = '',
        public readonly bool $isTransaction = false,
        public readonly bool $isActive = false,
        public readonly ?string $replyToEmail = null,
        public readonly ?string $replyToName = null,
        public readonly array $tags = [],
        public readonly object $utm = new stdClass,
        public readonly object $urlParams = new stdClass,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $utm = $data['utm'] ?? new stdClass;
        $urlParams = $data['urlParams'] ?? new stdClass;
        if (is_array($utm)) {
            $utm = (object) $utm;
        }
        if (is_array($urlParams)) {
            $urlParams = (object) $urlParams;
        }

        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            subject: $data['subject'] ?? '',
            fromEmail: $data['fromEmail'] ?? '',
            fromName: $data['fromName'] ?? '',
            html: $data['html'] ?? '',
            plainText: $data['plainText'] ?? '',
            isTransaction: (bool) ($data['isTransaction'] ?? false),
            isActive: (bool) ($data['isActive'] ?? false),
            replyToEmail: $data['replyToEmail'] ?? null,
            replyToName: $data['replyToName'] ?? null,
            tags: is_array($data['tags'] ?? null) ? $data['tags'] : [],
            utm: $utm,
            urlParams: $urlParams,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'subject' => $this->subject,
            'fromEmail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'html' => $this->html,
            'plainText' => $this->plainText,
            'isTransaction' => $this->isTransaction,
            'isActive' => $this->isActive,
            'replyToEmail' => $this->replyToEmail,
            'replyToName' => $this->replyToName,
            'tags' => $this->tags,
            'utm' => $this->utm,
            'urlParams' => $this->urlParams,
        ], fn($v) => $v !== null);
    }
}
