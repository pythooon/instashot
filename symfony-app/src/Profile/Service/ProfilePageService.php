<?php

declare(strict_types=1);

namespace App\Profile\Service;

use App\Profile\Dto\Response\ProfileViewResponse;
use App\Profile\Mapper\ProfileViewMapper;
use App\Auth\Repository\UserRepositoryInterface;

final readonly class ProfilePageService implements ProfilePageServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ProfileViewMapper $profileViewMapper,
    ) {
    }

    public function getProfileByUserId(int $userId): ?ProfileViewResponse
    {
        $user = $this->userRepository->find($userId);

        return $user !== null ? $this->profileViewMapper->toProfileView($user) : null;
    }
}
