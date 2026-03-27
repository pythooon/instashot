<?php

declare(strict_types=1);

namespace Tests\Unit\Photo\Mapper;

use App\Auth\Entity\User;
use App\Photo\Entity\Photo;
use App\Photo\Mapper\PhotoFeedMapper;
use PHPUnit\Framework\TestCase;

final class PhotoFeedMapperTest extends TestCase
{
    public function testToUserBriefMapsFields(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(5);
        $user->method('getUsername')->willReturn('bob');
        $user->method('getName')->willReturn('Bob');
        $user->method('getLastName')->willReturn('Builder');

        $brief = (new PhotoFeedMapper())->toUserBrief($user);

        $this->assertSame(5, $brief->getId());
        $this->assertSame('bob', $brief->getUsername());
        $this->assertSame('Bob', $brief->getName());
        $this->assertSame('Builder', $brief->getLastName());
    }

    public function testToUserBriefThrowsWhenIdMissing(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(null);

        $this->expectException(\LogicException::class);

        (new PhotoFeedMapper())->toUserBrief($user);
    }

    public function testToPhotoCardDelegatesAuthorAndLikeFlag(): void
    {
        $author = $this->createMock(User::class);
        $author->method('getId')->willReturn(2);
        $author->method('getUsername')->willReturn('author');
        $author->method('getName')->willReturn(null);
        $author->method('getLastName')->willReturn(null);

        $photo = $this->createMock(Photo::class);
        $photo->method('getId')->willReturn(99);
        $photo->method('getImageUrl')->willReturn('https://example.com/p.jpg');
        $photo->method('getDescription')->willReturn('Desc');
        $photo->method('getLocation')->willReturn('Kraków');
        $photo->method('getCamera')->willReturn('Nikon');
        $photo->method('getLikeCounter')->willReturn(7);
        $photo->method('getUser')->willReturn($author);

        $card = (new PhotoFeedMapper())->toPhotoCard($photo, true);

        $this->assertSame(99, $card->getId());
        $this->assertSame('https://example.com/p.jpg', $card->getImageUrl());
        $this->assertSame('Desc', $card->getDescription());
        $this->assertSame('Kraków', $card->getLocation());
        $this->assertSame('Nikon', $card->getCamera());
        $this->assertSame(7, $card->getLikeCounter());
        $this->assertTrue($card->isLikedByCurrentUser());
        $this->assertSame(2, $card->getAuthor()->getId());
    }
}
