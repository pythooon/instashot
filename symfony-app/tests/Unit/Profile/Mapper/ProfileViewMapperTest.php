<?php

declare(strict_types=1);

namespace Tests\Unit\Profile\Mapper;

use App\Auth\Entity\User;
use App\Profile\Mapper\ProfileViewMapper;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

final class ProfileViewMapperTest extends TestCase
{
    public function testMapsUserToProfileView(): void
    {
        $photos = $this->createMock(Collection::class);
        $photos->method('count')->willReturn(4);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(11);
        $user->method('getUsername')->willReturn('emma');
        $user->method('getEmail')->willReturn('emma@example.com');
        $user->method('getName')->willReturn('Emma');
        $user->method('getLastName')->willReturn('W');
        $user->method('getAge')->willReturn(30);
        $user->method('getBio')->willReturn('Bio text');
        $user->method('getPhotos')->willReturn($photos);
        $user->method('hasPhoenixAccessToken')->willReturn(true);

        $view = (new ProfileViewMapper())->toProfileView($user);

        $this->assertSame(11, $view->getId());
        $this->assertSame('emma', $view->getUsername());
        $this->assertSame('emma@example.com', $view->getEmail());
        $this->assertSame('Emma', $view->getName());
        $this->assertSame('W', $view->getLastName());
        $this->assertSame(30, $view->getAge());
        $this->assertSame('Bio text', $view->getBio());
        $this->assertSame(4, $view->getPhotosCount());
        $this->assertTrue($view->hasPhoenixAccessToken());
    }

    public function testThrowsWhenUserNotPersisted(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(null);

        $this->expectException(\LogicException::class);

        (new ProfileViewMapper())->toProfileView($user);
    }
}
