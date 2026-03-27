<?php

declare(strict_types=1);

namespace Tests\Unit\Photo\Service;

use App\Auth\Entity\User;
use App\Auth\Repository\UserRepositoryInterface;
use App\Like\Service\LikeServiceInterface;
use App\Photo\Entity\Photo;
use App\Photo\Dto\Request\HomeFeedFilterQuery;
use App\Photo\Mapper\PhotoFeedMapper;
use App\Photo\Repository\PhotoRepositoryInterface;
use App\Photo\Service\HomeFeedService;
use PHPUnit\Framework\TestCase;

final class HomeFeedServiceTest extends TestCase
{
    public function testBuildsEmptyFeedWhenNoPhotos(): void
    {
        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('countHomeFeedPhotos')->willReturn(0);
        $photoRepo->method('findHomeFeedPhotos')->willReturn([]);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $likeService = $this->createMock(LikeServiceInterface::class);
        $mapper = new PhotoFeedMapper();

        $service = new HomeFeedService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
            photoFeedMapper: $mapper,
        );
        $feed = $service->buildHomeFeed(null, new HomeFeedFilterQuery());

        $this->assertSame([], $feed->getPhotos());
        $this->assertNull($feed->getCurrentUser());
        $this->assertSame(0, $feed->getTotalPhotos());
        $this->assertSame(1, $feed->getPage());
    }

    public function testIgnoresStaleSessionUserIdWhenUserRemoved(): void
    {
        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('countHomeFeedPhotos')->willReturn(0);
        $photoRepo->method('findHomeFeedPhotos')->willReturn([]);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(42)->willReturn(null);

        $likeService = $this->createMock(LikeServiceInterface::class);
        $mapper = new PhotoFeedMapper();

        $service = new HomeFeedService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
            photoFeedMapper: $mapper,
        );
        $feed = $service->buildHomeFeed(42, new HomeFeedFilterQuery());

        $this->assertNull($feed->getCurrentUser());
    }

    public function testBuildsCardsAndQueriesLikeStateForCurrentUser(): void
    {
        $sessionUser = $this->createMock(User::class);
        $sessionUser->method('getId')->willReturn(1);
        $sessionUser->method('getUsername')->willReturn('me');
        $sessionUser->method('getName')->willReturn('Me');
        $sessionUser->method('getLastName')->willReturn('User');

        $author = $this->createMock(User::class);
        $author->method('getId')->willReturn(2);
        $author->method('getUsername')->willReturn('other');
        $author->method('getName')->willReturn(null);
        $author->method('getLastName')->willReturn(null);

        $photo = $this->createMock(Photo::class);
        $photo->method('getId')->willReturn(10);
        $photo->method('getImageUrl')->willReturn('https://example.com/x.jpg');
        $photo->method('getDescription')->willReturn(null);
        $photo->method('getLocation')->willReturn(null);
        $photo->method('getCamera')->willReturn(null);
        $photo->method('getLikeCounter')->willReturn(0);
        $photo->method('getUser')->willReturn($author);

        $photoRepo = $this->createMock(PhotoRepositoryInterface::class);
        $photoRepo->method('countHomeFeedPhotos')->willReturn(1);
        $photoRepo->method('findHomeFeedPhotos')->willReturn([$photo]);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($sessionUser);

        $likeService = $this->createMock(LikeServiceInterface::class);
        $likeService->expects($this->once())
            ->method('hasUserLikedPhoto')
            ->with($sessionUser, $photo)
            ->willReturn(true);

        $service = new HomeFeedService(
            photoRepository: $photoRepo,
            userRepository: $userRepo,
            likeService: $likeService,
            photoFeedMapper: new PhotoFeedMapper(),
        );
        $feed = $service->buildHomeFeed(1, new HomeFeedFilterQuery());

        $this->assertNotNull($feed->getCurrentUser());
        $this->assertSame(1, $feed->getCurrentUser()->getId());
        $photos = $feed->getPhotos();
        $this->assertCount(1, $photos);
        $this->assertTrue($photos[0]->isLikedByCurrentUser());
        $this->assertSame(10, $photos[0]->getId());
        $this->assertSame(1, $feed->getTotalPhotos());
    }
}
