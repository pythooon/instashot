<?php

declare(strict_types=1);

namespace Tests\Unit\Profile\Service;

use App\Auth\Entity\User;
use App\Auth\Repository\UserRepositoryInterface;
use App\Profile\Mapper\ProfileViewMapper;
use App\Profile\Service\ProfilePageService;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

final class ProfilePageServiceTest extends TestCase
{
    public function testReturnsNullWhenUserNotFound(): void
    {
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(404)->willReturn(null);

        $service = new ProfilePageService(
            userRepository: $userRepo,
            profileViewMapper: new ProfileViewMapper(),
        );

        $this->assertNull($service->getProfileByUserId(404));
    }

    public function testReturnsMappedProfileWhenUserExists(): void
    {
        $photos = $this->createMock(Collection::class);
        $photos->method('count')->willReturn(0);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getUsername')->willReturn('u');
        $user->method('getEmail')->willReturn('e@e.com');
        $user->method('getName')->willReturn(null);
        $user->method('getLastName')->willReturn(null);
        $user->method('getAge')->willReturn(null);
        $user->method('getBio')->willReturn(null);
        $user->method('getPhotos')->willReturn($photos);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $service = new ProfilePageService(
            userRepository: $userRepo,
            profileViewMapper: new ProfileViewMapper(),
        );
        $view = $service->getProfileByUserId(1);

        $this->assertNotNull($view);
        $this->assertSame(1, $view->getId());
        $this->assertSame('u', $view->getUsername());
        $this->assertSame('e@e.com', $view->getEmail());
        $this->assertSame(0, $view->getPhotosCount());
    }
}
