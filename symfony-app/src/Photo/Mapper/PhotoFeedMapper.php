<?php

declare(strict_types=1);

namespace App\Photo\Mapper;

use App\Photo\Entity\Photo;
use App\Auth\Entity\User;
use App\Photo\Dto\Response\PhotoCardResponse;
use App\Photo\Dto\Response\UserBriefResponse;

final readonly class PhotoFeedMapper
{
    public function toUserBrief(User $user): UserBriefResponse
    {
        $id = $this->requirePersistedPositiveId(
            $user->getId(),
            'User must be persisted before mapping.',
            'User id must be positive.',
        );

        return new UserBriefResponse(
            id: $id,
            username: $user->getUsername(),
            name: $user->getName(),
            lastName: $user->getLastName(),
        );
    }

    public function toPhotoCard(Photo $photo, bool $likedByCurrentUser): PhotoCardResponse
    {
        $id = $this->requirePersistedPositiveId(
            $photo->getId(),
            'Photo must be persisted before mapping.',
            'Photo id must be positive.',
        );

        return new PhotoCardResponse(
            id: $id,
            imageUrl: $photo->getImageUrl(),
            description: $photo->getDescription(),
            location: $photo->getLocation(),
            camera: $photo->getCamera(),
            likeCounter: $photo->getLikeCounter(),
            author: $this->toUserBrief($photo->getUser()),
            likedByCurrentUser: $likedByCurrentUser,
        );
    }

    private function requirePersistedPositiveId(?int $id, string $unpersistedMessage, string $nonPositiveMessage): int
    {
        if ($id === null) {
            throw new \LogicException($unpersistedMessage);
        }

        if ($id <= 0) {
            throw new \LogicException($nonPositiveMessage);
        }

        return $id;
    }
}
