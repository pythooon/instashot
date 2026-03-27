<?php

declare(strict_types=1);

namespace App\Photo\Service;

use App\Like\Enum\LikeToggleState;
use App\Like\Service\LikeServiceInterface;
use App\Photo\Dto\Request\TogglePhotoLikeRequest;
use App\Photo\Enum\TogglePhotoLikeResultType;
use App\Photo\Repository\PhotoRepositoryInterface;
use App\Auth\Repository\UserRepositoryInterface;

final readonly class PhotoLikeApplicationService implements PhotoLikeApplicationServiceInterface
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
        private UserRepositoryInterface $userRepository,
        private LikeServiceInterface $likeService,
    ) {
    }

    public function togglePhotoLike(TogglePhotoLikeRequest $request): TogglePhotoLikeResultType
    {
        $user = $this->userRepository->find($request->getUserId());
        if ($user === null) {
            return TogglePhotoLikeResultType::UserNotFound;
        }

        $photo = $this->photoRepository->find($request->getPhotoId());
        if ($photo === null) {
            return TogglePhotoLikeResultType::PhotoNotFound;
        }

        return match ($this->likeService->toggleLike($user, $photo)) {
            LikeToggleState::Liked => TogglePhotoLikeResultType::Liked,
            LikeToggleState::Unliked => TogglePhotoLikeResultType::Unliked,
        };
    }
}
