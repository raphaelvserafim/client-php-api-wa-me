<?php

namespace Api\Wame;

/**
 * Provider channel a message is sent through / received from.
 *
 * Used as the client-level default (see {@see Wame}) and as the optional
 * `provider` field on message sends. When omitted, the API assumes `whatsapp`.
 */
final class Provider
{
    public const WHATSAPP = 'whatsapp';
    public const INSTAGRAM = 'instagram';
    public const MESSENGER = 'messenger';

    /** All valid provider values. */
    public const ALL = [self::WHATSAPP, self::INSTAGRAM, self::MESSENGER];

    /** @return bool True when $value is a recognized provider. */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::ALL, true);
    }
}
