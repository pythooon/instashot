<?php

declare(strict_types=1);

namespace App\Like\Service;

use App\Auth\Entity\User;
use App\Like\Enum\LikeToggleState;
use App\Like\Repository\LikeRepositoryInterface;
use App\Photo\Entity\Photo;

final readonly class LikeService implements LikeServiceInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
    ) {
    }

    public function hasUserLikedPhoto(User $user, Photo $photo): bool
    {
        return $this->likeRepository->hasUserLikedPhoto($user, $photo);
    }

    public function toggleLike(User $user, Photo $photo): LikeToggleState
    {
        if ($this->likeRepository->hasUserLikedPhoto($user, $photo)) {
            $this->likeRepository->unlikePhoto($user, $photo);

            return LikeToggleState::Unliked;
        }

        $this->likeRepository->createLike($user, $photo);
        $this->likeRepository->updatePhotoCounter($photo, 1);

        return LikeToggleState::Liked;
    }
}
