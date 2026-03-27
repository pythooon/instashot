<?php

declare(strict_types=1);

namespace App\Profile\Service;

use App\Auth\Repository\UserRepositoryInterface;
use App\Integration\Phoenix\Exception\PhoenixApiException;
use App\Integration\Phoenix\Exception\PhoenixApiUnauthorizedException;
use App\Integration\Phoenix\PhoenixPhotosClientInterface;
use App\Photo\Repository\PhotoRepositoryInterface;
use App\Profile\Dto\ImportPhoenixPhotosResult;
use App\Profile\Mapper\ImportedPhoenixPhotoMapper;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ImportPhoenixPhotosService implements ImportPhoenixPhotosServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PhotoRepositoryInterface $photoRepository,
        private PhoenixPhotosClientInterface $phoenixPhotosClient,
        private ImportedPhoenixPhotoMapper $importedPhoenixPhotoMapper,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function importForUserId(int $userId): ImportPhoenixPhotosResult
    {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            return ImportPhoenixPhotosResult::userNotFound();
        }

        $token = $user->getPhoenixAccessToken();
        if ($token === null || $token === '') {
            return ImportPhoenixPhotosResult::noTokenConfigured();
        }

        try {
            $remote = $this->phoenixPhotosClient->fetchPhotosForToken($token);
        } catch (PhoenixApiUnauthorizedException) {
            return ImportPhoenixPhotosResult::invalidToken();
        } catch (PhoenixApiException) {
            return ImportPhoenixPhotosResult::requestFailed();
        }

        $imported = 0;
        foreach ($remote as $item) {
            if ($this->photoRepository->userHasPhotoWithImageUrl($user, $item->getPhotoUrl())) {
                continue;
            }

            $photo = $this->importedPhoenixPhotoMapper->createPhoto($item, $user);
            $this->entityManager->persist($photo);
            ++$imported;
        }

        $this->entityManager->flush();

        return ImportPhoenixPhotosResult::success($imported);
    }
}
