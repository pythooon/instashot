<?php

declare(strict_types=1);

namespace App\Profile\Validation;

final class PhoenixAccessTokenValidator
{
    public const int MAX_LENGTH = 512;

    private const string ASCII_TOKEN_PATTERN = '/^[a-zA-Z0-9._-]+$/';

    public function isValid(string $normalized): bool
    {
        if (\strlen($normalized) > self::MAX_LENGTH) {
            return false;
        }

        return preg_match(self::ASCII_TOKEN_PATTERN, $normalized) === 1;
    }
}
