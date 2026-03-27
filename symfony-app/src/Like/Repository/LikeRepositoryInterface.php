<?php

declare(strict_types=1);

namespace App\Like\Repository;

use App\Auth\Entity\User;
use App\Like\Entity\Like;
use App\Photo\Entity\Photo;

interface LikeRepositoryInterface
{
    public function unlikePhoto(User $user, Photo $photo): void;

    public function hasUserLikedPhoto(User $user, Photo $photo): bool;

    public function createLike(User $user, Photo $photo): Like;

    public function updatePhotoCounter(Photo $photo, int $increment): void;
}
