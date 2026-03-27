<?php

declare(strict_types=1);

namespace App\Profile\Enum;

enum SavePhoenixAccessTokenOutcome
{
    case Success;
    case UserNotFound;
    case InvalidTokenFormat;
}
