<?php

declare(strict_types=1);

namespace App\Auth\Mapper;

use App\Auth\Dto\Response\AuthenticatedUserResponse;
use App\Auth\Entity\User;

final readonly class AuthenticatedUserMapper
{
    public function toAuthenticatedUser(User $user): AuthenticatedUserResponse
    {
        $id = $user->getId();
        if ($id === null) {
            throw new \LogicException('User must be persisted before mapping.');
        }

        if ($id <= 0) {
            throw new \LogicException('User id must be positive.');
        }

        return new AuthenticatedUserResponse(
            id: $id,
            username: $user->getUsername(),
        );
    }
}
