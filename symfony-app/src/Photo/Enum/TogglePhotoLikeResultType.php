<?php

declare(strict_types=1);

namespace App\Photo\Enum;

enum TogglePhotoLikeResultType
{
    case Liked;
    case Unliked;
    case PhotoNotFound;
    case UserNotFound;
}
