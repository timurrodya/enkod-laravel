<?php

namespace Timurrodya\Enkod\Contracts;

interface Dtoable
{
    public static function fromArray(array $data): self;

    public function toArray(): array;
}
