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
        $this->validateAttachments();
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

    protected function validateAttachments(): void
    {
        if ($this->attachments === null) {
            return;
        }

        foreach ($this->attachments as $attachment) {
            if (! $attachment instanceof AttachmentDto) {
                throw new InvalidArgumentException(
                    "All attachments must be instances of AttachmentDto"
                );
            }
        }
    }

    public static function fromArray(array $data): self
    {
        $attachments = isset($data['attachments'])
            ? array_map(
                fn(array $a) => AttachmentDto::fromArray($a),
                $data['attachments']
            )
            : null;

        return new self(
            $data['messageId'],
            $data['email'],
            $data['snippets'] ?? null,
            $attachments
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'messageId'   => $this->messageId,
            'email'       => $this->email,
            'snippets'    => $this->snippets,
            'attachments' => $this->attachments
                ? array_map(
                    fn(AttachmentDto $a) => $a->toArray(),
                    $this->attachments
                )
                : null,
        ], fn($value) => $value !== null);
    }
}
