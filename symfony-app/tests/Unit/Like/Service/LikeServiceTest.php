<?php

declare(strict_types=1);

namespace Tests\Unit\Like\Service;

use App\Auth\Entity\User;
use App\Like\Enum\LikeToggleState;
use App\Like\Repository\LikeRepositoryInterface;
use App\Like\Service\LikeService;
use App\Photo\Entity\Photo;
use PHPUnit\Framework\TestCase;

final class LikeServiceTest extends TestCase
{
    public function testToggleCreatesLikeAndIncrementsWhenNotLikedYet(): void
    {
        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $repo = $this->createMock(LikeRepositoryInterface::class);
        $repo->method('hasUserLikedPhoto')->with($user, $photo)->willReturn(false);
        $repo->expects($this->once())->method('createLike')->with($user, $photo);
        $repo->expects($this->once())->method('updatePhotoCounter')->with($photo, 1);
        $repo->expects($this->never())->method('unlikePhoto');

        $service = new LikeService($repo);
        $this->assertSame(LikeToggleState::Liked, $service->toggleLike($user, $photo));
    }

    public function testToggleRemovesLikeWhenAlreadyLiked(): void
    {
        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $repo = $this->createMock(LikeRepositoryInterface::class);
        $repo->method('hasUserLikedPhoto')->with($user, $photo)->willReturn(true);
        $repo->expects($this->once())->method('unlikePhoto')->with($user, $photo);
        $repo->expects($this->never())->method('createLike');

        $service = new LikeService($repo);
        $this->assertSame(LikeToggleState::Unliked, $service->toggleLike($user, $photo));
    }

    public function testHasUserLikedPhotoDelegatesToRepository(): void
    {
        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $repo = $this->createMock(LikeRepositoryInterface::class);
        $repo->method('hasUserLikedPhoto')->with($user, $photo)->willReturn(true);

        $service = new LikeService($repo);
        $this->assertTrue($service->hasUserLikedPhoto($user, $photo));
    }
}
