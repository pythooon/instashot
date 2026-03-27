<?php

declare(strict_types=1);

namespace Tests\Unit\Photo\Service;

use App\Auth\Entity\User;
use App\Auth\Repository\UserRepositoryInterface;
use App\Like\Enum\LikeToggleState;
use App\Like\Service\LikeServiceInterface;
use App\Photo\Dto\Request\TogglePhotoLikeRequest;
use App\Photo\Entity\Photo;
use App\Photo\Enum\TogglePhotoLikeResultType;
use App\Photo\Repository\PhotoRepositoryInterface;
use App\Photo\Service\PhotoLikeApplicationService;
use PHPUnit\Framework\TestCase;

final class PhotoLikeApplicationServiceTest extends TestCase
{
    public function testReturnsUserNotFoundWhenUserMissing(): void
    {
        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn(null);
        $likeService = $this->createMock(LikeServiceInterface::class);

        $service = new PhotoLikeApplicationService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
        );
        $result = $service->togglePhotoLike(new TogglePhotoLikeRequest(photoId: 10, userId: 1));

        $this->assertSame(TogglePhotoLikeResultType::UserNotFound, $result);
    }

    public function testReturnsPhotoNotFoundWhenPhotoMissing(): void
    {
        $user = $this->createMock(User::class);

        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('find')->with(99)->willReturn(null);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $likeService = $this->createMock(LikeServiceInterface::class);

        $service = new PhotoLikeApplicationService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
        );
        $result = $service->togglePhotoLike(new TogglePhotoLikeRequest(photoId: 99, userId: 1));

        $this->assertSame(TogglePhotoLikeResultType::PhotoNotFound, $result);
    }

    public function testMapsLikedStateFromLikeModule(): void
    {
        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('find')->with(5)->willReturn($photo);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(2)->willReturn($user);

        $likeService = $this->createMock(LikeServiceInterface::class);
        $likeService->expects($this->once())
            ->method('toggleLike')
            ->with($user, $photo)
            ->willReturn(LikeToggleState::Liked);

        $service = new PhotoLikeApplicationService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
        );
        $result = $service->togglePhotoLike(new TogglePhotoLikeRequest(photoId: 5, userId: 2));

        $this->assertSame(TogglePhotoLikeResultType::Liked, $result);
    }

    public function testMapsUnlikedStateFromLikeModule(): void
    {
        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('find')->willReturn($photo);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->willReturn($user);

        $likeService = $this->createMock(LikeServiceInterface::class);
        $likeService->method('toggleLike')->willReturn(LikeToggleState::Unliked);

        $service = new PhotoLikeApplicationService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
        );
        $result = $service->togglePhotoLike(new TogglePhotoLikeRequest(photoId: 5, userId: 2));

        $this->assertSame(TogglePhotoLikeResultType::Unliked, $result);
    }
}
