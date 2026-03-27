<?php

declare(strict_types=1);

namespace Tests\Unit\Profile\Mapper;

use App\Auth\Entity\User;
use App\Integration\Phoenix\Dto\PhoenixPhotoData;
use App\Profile\Mapper\ImportedPhoenixPhotoMapper;
use PHPUnit\Framework\TestCase;

final class ImportedPhoenixPhotoMapperTest extends TestCase
{
    public function testMapsAllFieldsAndOwner(): void
    {
        $owner = (new User())
            ->setUsername('u')
            ->setEmail('u@example.org');

        $takenAt = new \DateTimeImmutable('2024-06-01T10:00:00Z');
        $data = new PhoenixPhotoData(
            id: 1,
            photoUrl: 'https://cdn.example/p.jpg',
            camera: 'Nikon',
            description: 'Sunset',
            location: 'Gdańsk',
            takenAt: $takenAt,
        );

        $photo = (new ImportedPhoenixPhotoMapper())->createPhoto($data, $owner);

        $this->assertSame('https://cdn.example/p.jpg', $photo->getImageUrl());
        $this->assertSame('Nikon', $photo->getCamera());
        $this->assertSame('Sunset', $photo->getDescription());
        $this->assertSame('Gdańsk', $photo->getLocation());
        $this->assertSame($takenAt, $photo->getTakenAt());
        $this->assertSame($owner, $photo->getUser());
    }
}
