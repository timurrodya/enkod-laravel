<?php

namespace Timurrodya\Enkod\Facades;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;
use Timurrodya\Enkod\Enkod as BaseEnkod;

/**
 * Class Enkod
 *
 * @package Timurrodya\Enkod\Facades
 * @method bool mail(int $messageId, string $email, array $snippets = [], array $attachments = [])
 * @method bool mails(int $messageId, object $recipients)
 * @method array messageCreate(string $subject, string $fromEmail, string $fromName, string $html, string $plainText, bool $isTransaction = false, bool $isActive = false, string $replyToEmail = null, string $replyToName = null, array $tags = [], object $utm = new stdClass, object $urlParams = new stdClass)
 * @method array messageOnetime(object $message, bool $isDraft = false, object $to = null, Carbon $deliveryDate = null)
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
