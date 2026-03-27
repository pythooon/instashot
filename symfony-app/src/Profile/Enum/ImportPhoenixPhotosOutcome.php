<?php

declare(strict_types=1);

namespace App\Profile\Enum;

enum ImportPhoenixPhotosOutcome
{
    case Success;
    case NoTokenConfigured;
    case InvalidToken;
    case UserNotFound;
    case RequestFailed;
}
