<?php

namespace Timurrodya\Enkod\Dto;

use InvalidArgumentException;
use Timurrodya\Enkod\Contracts\Dtoable;

class SmtpBatchEmailDto implements Dtoable
{
    public function __construct(
        public readonly string $sendingDomain,
        public readonly string $fromEmail,
        public readonly string $fromName,
        public readonly string|array $to,
        public readonly string $subject,
        public readonly ?string $html = null,
        public readonly ?string $body = null,
        public readonly ?array $attachments = null,
        public readonly ?string $url = null
    ) {
        $this->validate();
        $this->validateAttachments();
    }

    protected function validate(): void
    {
        if (!filter_var($this->fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'fromEmail' format");
        }
        if (is_string($this->to)) {
            if(!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Invalid 'to' email format");
            }
        } elseif (is_array($this->to)) {
            foreach ($this->to as $email) {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("Invalid 'to' email format");
                }
            }
        }
        if(!$this->sendingDomain) {
            throw new InvalidArgumentException("Missing 'sendingDomain'");
        }
        if(!$this->fromName) {
            throw new InvalidArgumentException("Missing 'fromName'");
        }
        if(!$this->subject) {
            throw new InvalidArgumentException("Missing 'subject'");
        }
    }

    protected function validateAttachments(): void
    {
        if ($this->attachments === null) return;
        foreach ($this->attachments as $attachment) {
            if (!$attachment instanceof AttachmentDto) {
                throw new InvalidArgumentException("All attachments must be instances of AttachmentDto");
            }
        }
    }

    public static function fromArray(array $data): self
    {
        $attachments = isset($data['attachments'])
            ? array_map(fn($a) => AttachmentDto::fromArray($a), $data['attachments'])
            : null;
        return new self(
            $data['sendingDomain'],
            $data['fromEmail'],
            $data['fromName'],
            $data['to'],
            $data['subject'],
            $data['html'] ?? null,
            $data['body'] ?? null,
            $attachments,
            $data['url'] ?? null
        );
    }
    public function toArray(): array
    {
        return array_filter([
            'sendingDomain' => $this->sendingDomain,
            'fromEmail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'to' => $this->to,
            'subject' => $this->subject,
            'html' => $this->html,
            'body' => $this->body,
            'attachments' => $this->attachments ? array_map(fn(AttachmentDto $a) => $a->toArray(), $this->attachments) : null,
            'url' => $this->url
        ], fn($v) => $v !== null);
    }
}

