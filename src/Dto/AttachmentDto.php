<?php

namespace Timurrodya\Enkod\Dto;

use InvalidArgumentException;
use Timurrodya\Enkod\Contracts\Dtoable;

class AttachmentDto implements Dtoable
{
    public function __construct(
        public readonly string $fileName,
        public readonly string $mimeType,
        public readonly string $content // base64 encoded
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if (empty($this->fileName)) {
            throw new InvalidArgumentException("File name is required");
        }

        if (empty($this->mimeType)) {
            throw new InvalidArgumentException("MIME type is required");
        }

        if (empty($this->content)) {
            throw new InvalidArgumentException("Content is required");
        }

        if (base64_decode($this->content, true) === false) {
            throw new InvalidArgumentException("Invalid base64 content");
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['fileName'],
            $data['mimeType'],
            $data['content']
        );
    }

    public function toArray(): array
    {
        return [
            'fileName' => $this->fileName,
            'mimeType' => $this->mimeType,
            'content'  => $this->content,
        ];
    }
}
