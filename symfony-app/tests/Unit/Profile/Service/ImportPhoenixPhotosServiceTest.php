<?php

declare(strict_types=1);

namespace Tests\Unit\Profile\Service;

use App\Auth\Entity\User;
use App\Auth\Repository\UserRepositoryInterface;
use App\Integration\Phoenix\Dto\PhoenixPhotoData;
use App\Integration\Phoenix\Exception\PhoenixApiUnauthorizedException;
use App\Integration\Phoenix\PhoenixPhotosClientInterface;
use App\Photo\Repository\PhotoRepositoryInterface;
use App\Profile\Enum\ImportPhoenixPhotosOutcome;
use App\Profile\Mapper\ImportedPhoenixPhotoMapper;
use App\Profile\Service\ImportPhoenixPhotosService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ImportPhoenixPhotosServiceTest extends TestCase
{
    public function testReturnsNoTokenWhenUserHasNoPhoenixToken(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPhoenixAccessToken')->willReturn(null);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $service = new ImportPhoenixPhotosService(
            userRepository: $userRepo,
            photoRepository: $this->createMock(PhotoRepositoryInterface::class),
            phoenixPhotosClient: $this->createMock(PhoenixPhotosClientInterface::class),
            importedPhoenixPhotoMapper: new ImportedPhoenixPhotoMapper(),
            entityManager: $this->createMock(EntityManagerInterface::class),
        );

        $r = $service->importForUserId(1);
        $this->assertSame(ImportPhoenixPhotosOutcome::NoTokenConfigured, $r->getOutcome());
    }

    public function testReturnsInvalidTokenOnUnauthorized(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPhoenixAccessToken')->willReturn('bad');

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $client = $this->createMock(PhoenixPhotosClientInterface::class);
        $client->method('fetchPhotosForToken')->willThrowException(new PhoenixApiUnauthorizedException());

        $service = new ImportPhoenixPhotosService(
            userRepository: $userRepo,
            photoRepository: $this->createMock(PhotoRepositoryInterface::class),
            phoenixPhotosClient: $client,
            importedPhoenixPhotoMapper: new ImportedPhoenixPhotoMapper(),
            entityManager: $this->createMock(EntityManagerInterface::class),
        );

        $r = $service->importForUserId(1);
        $this->assertSame(ImportPhoenixPhotosOutcome::InvalidToken, $r->getOutcome());
    }

    public function testPersistsOnlyNewPhotosByImageUrl(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPhoenixAccessToken')->willReturn('ok');

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $item = new PhoenixPhotoData(
            id: 9,
            photoUrl: 'https://ex/new.jpg',
            camera: 'Cam',
            description: 'Hi',
            location: 'Wawa',
            takenAt: new \DateTimeImmutable('2024-01-01T12:00:00Z'),
        );

        $client = $this->createMock(PhoenixPhotosClientInterface::class);
        $client->method('fetchPhotosForToken')->willReturn([$item]);

        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('userHasPhotoWithImageUrl')->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->with($this->callback(function ($p): bool {
            return $p instanceof \App\Photo\Entity\Photo
                && $p->getImageUrl() === 'https://ex/new.jpg'
                && $p->getCamera() === 'Cam';
        }));
        $em->expects($this->once())->method('flush');

        $service = new ImportPhoenixPhotosService(
            userRepository: $userRepo,
            photoRepository: $photoRepo,
            phoenixPhotosClient: $client,
            importedPhoenixPhotoMapper: new ImportedPhoenixPhotoMapper(),
            entityManager: $em,
        );

        $r = $service->importForUserId(1);
        $this->assertSame(ImportPhoenixPhotosOutcome::Success, $r->getOutcome());
        $this->assertSame(1, $r->getImportedCount());
    }
}
