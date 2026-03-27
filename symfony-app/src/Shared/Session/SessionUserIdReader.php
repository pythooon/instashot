<?php

declare(strict_types=1);

namespace App\Shared\Session;

final class SessionUserIdReader
{
    public static function fromSessionValue(mixed $userId): ?int
    {
        if (is_int($userId)) {
            return $userId;
        }

        if (is_numeric($userId)) {
            return (int) $userId;
        }

        return null;
    }
}
