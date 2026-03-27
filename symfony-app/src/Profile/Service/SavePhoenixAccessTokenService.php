<?php

declare(strict_types=1);

namespace App\Profile\Service;

use App\Auth\Repository\UserRepositoryInterface;
use App\Profile\Dto\SavePhoenixAccessTokenResult;
use App\Profile\Validation\PhoenixAccessTokenValidator;
use App\Shared\Text\InputNormalizer;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SavePhoenixAccessTokenService implements SavePhoenixAccessTokenServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $entityManager,
        private PhoenixAccessTokenValidator $phoenixAccessTokenValidator,
    ) {
    }

    public function saveForUserId(
        int $userId,
        string $rawToken,
        bool $removeToken = false,
    ): SavePhoenixAccessTokenResult {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            return SavePhoenixAccessTokenResult::userNotFound();
        }

        if ($removeToken) {
            $user->setPhoenixAccessToken(null);
            $this->entityManager->flush();

            return SavePhoenixAccessTokenResult::success();
        }

        $normalized = InputNormalizer::trimCopyPasteArtifacts($rawToken);
        if ($normalized === '') {
            return SavePhoenixAccessTokenResult::success();
        }

        if (!$this->phoenixAccessTokenValidator->isValid($normalized)) {
            return SavePhoenixAccessTokenResult::invalidTokenFormat();
        }

        $user->setPhoenixAccessToken($normalized);
        $this->entityManager->flush();

        return SavePhoenixAccessTokenResult::success();
    }
}
