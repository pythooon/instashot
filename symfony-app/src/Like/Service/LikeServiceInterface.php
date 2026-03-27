<?php

declare(strict_types=1);

namespace App\Like\Service;

use App\Auth\Entity\User;
use App\Like\Enum\LikeToggleState;
use App\Photo\Entity\Photo;

interface LikeServiceInterface
{
    public function hasUserLikedPhoto(User $user, Photo $photo): bool;

    public function toggleLike(User $user, Photo $photo): LikeToggleState;
}
