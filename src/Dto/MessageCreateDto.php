<?php

namespace Timurrodya\Enkod\Dto;

use InvalidArgumentException;
use stdClass;
use Timurrodya\Enkod\Contracts\Dtoable;

class MessageCreateDto implements Dtoable
{
    public function __construct(
        public readonly string $subject,
        public readonly string $fromEmail,
        public readonly string $fromName,
        public readonly string $html,
        public readonly string $plainText,
        public readonly bool $isTransaction = false,
        public readonly bool $isActive = false,
        public readonly ?string $replyToEmail = null,
        public readonly ?string $replyToName = null,
        public readonly array $tags = [],
        public readonly object $utm = new stdClass,
        public readonly object $urlParams = new stdClass,
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if ($this->subject === '') {
            throw new InvalidArgumentException("'subject' cannot be empty");
        }
        if (! filter_var($this->fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'fromEmail' format");
        }
        if ($this->replyToEmail !== null && ! filter_var($this->replyToEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid 'replyToEmail' format");
        }
        if ($this->fromName === '') {
            throw new InvalidArgumentException("'fromName' cannot be empty");
        }
        if ($this->html === '' && $this->plainText === '') {
            throw new InvalidArgumentException("Either 'html' or 'plainText' must be provided");
        }
        if (! is_array($this->tags)) {
            throw new InvalidArgumentException("'tags' must be array");
        }
        if (! is_object($this->utm)) {
            throw new InvalidArgumentException("'utm' must be object");
        }
        if (! is_object($this->urlParams)) {
            throw new InvalidArgumentException("'urlParams' must be object");
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['subject'],
            $data['fromEmail'],
            $data['fromName'],
            $data['html'],
            $data['plainText'],
            $data['isTransaction'] ?? false,
            $data['isActive'] ?? false,
            $data['replyToEmail'] ?? null,
            $data['replyToName'] ?? null,
            $data['tags'] ?? [],
            $data['utm'] ?? new stdClass,
            $data['urlParams'] ?? new stdClass,
        );
    }

    public function toArray(): array
    {
        return [
            'subject'       => $this->subject,
            'fromEmail'     => $this->fromEmail,
            'fromName'      => $this->fromName,
            'html'          => $this->html,
            'plainText'     => $this->plainText,
            'isTransaction' => $this->isTransaction,
            'isActive'      => $this->isActive,
            'replyToEmail'  => $this->replyToEmail,
            'replyToName'   => $this->replyToName,
            'tags'          => $this->tags,
            'utm'           => $this->utm,
            'urlParams'     => $this->urlParams,
        ];
    }
}

