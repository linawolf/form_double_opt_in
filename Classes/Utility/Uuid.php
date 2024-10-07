<?php

namespace LinaWolf\FormDoubleOptIn\Utility;

class Uuid
{
    /**
     * Generate a UUID.
     *
     * @return string
     */
    public static function generate(): string
    {
        $bytes = function_exists('random_bytes') ? random_bytes(16) : openssl_random_pseudo_bytes(16);
        $hash = bin2hex($bytes);
        return self::uuidFromHash($hash, 4);
    }

    /**
     * Generate the UUID from hash.
     *
     * @return string
     */
    private static function uuidFromHash(string $hash, int $version): string
    {
        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | $version << 12,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12),
        );
    }
}
