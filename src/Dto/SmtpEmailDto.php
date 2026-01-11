<?php

namespace Timurrodya\Enkod\Dto;

use InvalidArgumentException;
use Timurrodya\Enkod\Contracts\Dtoable;

class SmtpEmailDto implements Dtoable
{
    public function __construct(
        public readonly string|array $to,
        public readonly string $subject,
        public readonly ?string $body = null,
        public readonly ?string $html = null,
        public readonly ?string $from = null,
        public readonly ?string $fromName = null,
        public readonly string|array|null $cc = null,
        public readonly string|array|null $bcc = null,
        public readonly ?string $replyTo = null,
        public readonly ?array $attachments = null
    ) {
        $this->validate();
        $this->validateAttachments();
    }

    protected function validate(): void
    {
        if (is_string($this->to) && ! filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'to' email format");
        }

        if (is_array($this->to)) {
            foreach ($this->to as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("Invalid email format in 'to' array: {$email}");
                }
            }
        }

        if ($this->from !== null && ! filter_var($this->from, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'from' email format");
        }

        if ($this->replyTo !== null && ! filter_var($this->replyTo, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'replyTo' email format");
        }

        if (is_string($this->cc) && ! filter_var($this->cc, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'cc' email format");
        }

        if (is_array($this->cc)) {
            foreach ($this->cc as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("Invalid email format in 'cc' array: {$email}");
                }
            }
        }

        if (is_string($this->bcc) && ! filter_var($this->bcc, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'bcc' email format");
        }

        if (is_array($this->bcc)) {
            foreach ($this->bcc as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("Invalid email format in 'bcc' array: {$email}");
                }
            }
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
            $data['to'],
            $data['subject'],
            $data['body'] ?? null,
            $data['html'] ?? null,
            $data['from'] ?? null,
            $data['fromName'] ?? null,
            $data['cc'] ?? null,
            $data['bcc'] ?? null,
            $data['replyTo'] ?? null,
            $attachments
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'to'          => $this->to,
            'subject'     => $this->subject,
            'body'        => $this->body,
            'html'        => $this->html,
            'from'        => $this->from,
            'fromName'    => $this->fromName,
            'cc'          => $this->cc,
            'bcc'         => $this->bcc,
            'replyTo'     => $this->replyTo,
            'attachments' => $this->attachments
                ? array_map(
                    fn(AttachmentDto $a) => $a->toArray(),
                    $this->attachments
                )
                : null,
        ], fn($value) => $value !== null);
    }
}

