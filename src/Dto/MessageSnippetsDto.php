<?php

namespace Timurrodya\Enkod\Dto;

use Timurrodya\Enkod\Contracts\Dtoable;

/**
 * DTO ответа GET /v1/message/{id}/snippets/ — список сниппетов сообщения.
 */
class MessageSnippetsDto implements Dtoable
{
    /**
     * @param int|null $messageId   Идентификатор сообщения/шаблона
     * @param array    $snippets    Ассоциативный массив сниппетов
     */
    public function __construct(
        public readonly ?int $messageId = null,
        public readonly array $snippets = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            messageId: isset($data['id']) ? (int) $data['id'] : (isset($data['messageId']) ? (int) $data['messageId'] : null),
            snippets: is_array($data['snippets'] ?? null) ? $data['snippets'] : [],
        );
    }

    public function toArray(): array
    {
        return [
            'messageId' => $this->messageId,
            'snippets'  => $this->snippets,
        ];
    }
}
