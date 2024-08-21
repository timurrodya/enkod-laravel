<?php

namespace Timurrodya\Enkod\Facades;

use Illuminate\Support\Facades\Facade;
use Timurrodya\Enkod\Enkod as BaseEnkod;

/**
 * Class Enkod
 *
 * @package Timurrodya\Enkod\Facades
 * @method bool mail(int $messageId, string $email, array $snippets = [], array $attachments = [])
 * @method bool mails(int $messageId, object $recipients)
 *
 * @see BaseEnkod
 */
class Enkod extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'enkod';
    }
}
