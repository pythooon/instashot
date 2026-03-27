<?php

declare(strict_types=1);

namespace App\Auth\Enum;

enum LoginFailureType
{
    case InvalidToken;
    case UserNotFound;
}
