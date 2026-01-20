<?php

namespace Timurrodya\Enkod\Dto;

use Carbon\Carbon;
use InvalidArgumentException;
use stdClass;
use Timurrodya\Enkod\Contracts\Dtoable;

/**
 * DTO для создания мгновенного, запланированного или черновика сообщения.
 */
class MessageOnetimeDto implements Dtoable
{
    public function __construct(
        public readonly MessageCreateDto $message,
        public readonly bool $isDraft = true,
        public readonly ?object $to = null,
        public readonly ?Carbon $deliveryDate = null,
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if ($this->to !== null && ! is_object($this->to)) {
            throw new InvalidArgumentException("'to' must be an object when provided");
        }
    }

    public static function fromArray(array $data): self
    {
        $message = isset($data['message'])
            ? (is_array($data['message'])
                ? MessageCreateDto::fromArray($data['message'])
                : ($data['message'] instanceof MessageCreateDto ? $data['message'] : null))
            : null;

        if (! $message instanceof MessageCreateDto) {
            throw new InvalidArgumentException("'message' must be MessageCreateDto or array");
        }

        $to = isset($data['to']) && is_array($data['to'])
            ? (object) $data['to']
            : ($data['to'] ?? null);

        $deliveryDate = isset($data['deliveryDate']) && $data['deliveryDate'] !== null
            ? Carbon::parse($data['deliveryDate'])
            : null;

        return new self(
            message: $message,
            isDraft: $data['isDraft'] ?? true,
            to: $to,
            deliveryDate: $deliveryDate,
        );
    }

    public function toArray(): array
    {
        return [
            'message'      => $this->message->toArray(),
            'isDraft'      => $this->isDraft,
            'to'           => $this->to,
            'deliveryDate' => $this->deliveryDate?->format('Y-m-d H:i'),
        ];
    }
}
