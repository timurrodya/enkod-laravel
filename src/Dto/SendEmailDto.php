<?php

namespace Timurrodya\Enkod\Dto;

use InvalidArgumentException;
use Timurrodya\Enkod\Contracts\Dtoable;

class SendEmailDto implements Dtoable
{
    public function __construct(
        public readonly int $messageId,
        public readonly string $email,
        public readonly ?array $snippets = null,
        public readonly ?array $attachments = null
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if ($this->messageId <= 0) {
            throw new InvalidArgumentException("Message ID must be positive");
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['messageId'],
            $data['email'],
            $data['snippets'] ?? null,
            $data['attachments'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'messageId'   => $this->messageId,
            'email'       => $this->email,
            'snippets'    => $this->snippets,
            'attachments' => $this->attachments,
        ], fn($value) => ! is_null($value));
    }
}
