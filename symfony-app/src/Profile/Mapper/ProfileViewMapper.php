<?php

declare(strict_types=1);

namespace App\Profile\Mapper;

use App\Auth\Entity\User;
use App\Profile\Dto\Response\ProfileViewResponse;

final readonly class ProfileViewMapper
{
    public function toProfileView(User $user): ProfileViewResponse
    {
        $id = $user->getId();
        if ($id === null) {
            throw new \LogicException('User must be persisted before mapping.');
        }

        if ($id <= 0) {
            throw new \LogicException('User id must be positive.');
        }

        return new ProfileViewResponse(
            id: $id,
            username: $user->getUsername(),
            email: $user->getEmail(),
            name: $user->getName(),
            lastName: $user->getLastName(),
            age: $user->getAge(),
            bio: $user->getBio(),
            photosCount: $user->getPhotos()->count(),
            hasPhoenixAccessToken: $user->hasPhoenixAccessToken(),
        );
    }
}
