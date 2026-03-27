<?php

declare(strict_types=1);

namespace Tests\Unit\Integration\Phoenix\Mapper;

use App\Integration\Phoenix\Mapper\PhoenixPhotoJsonMapper;
use PHPUnit\Framework\TestCase;

final class PhoenixPhotoJsonMapperTest extends TestCase
{
    public function testMapPhotosPayloadReturnsEmptyWhenPhotosMissing(): void
    {
        $mapper = new PhoenixPhotoJsonMapper();
        $this->assertSame([], $mapper->mapPhotosPayload([]));
        $this->assertSame([], $mapper->mapPhotosPayload(['photos' => 'not-array']));
    }

    public function testSkipsInvalidRowsAndMapsValidOnes(): void
    {
        $mapper = new PhoenixPhotoJsonMapper();
        $out = $mapper->mapPhotosPayload([
            'photos' => [
                'not-array',
                ['id' => 'x', 'photo_url' => 'https://a.test/1.jpg'],
                ['id' => 1, 'photo_url' => 'https://a.test/ok.jpg', 'camera' => 'Canon'],
            ],
        ]);

        $this->assertCount(1, $out);
        $this->assertSame(1, $out[0]->getId());
        $this->assertSame('https://a.test/ok.jpg', $out[0]->getPhotoUrl());
        $this->assertSame('Canon', $out[0]->getCamera());
    }

    public function testMapPhotoRowReturnsNullForMissingUrl(): void
    {
        $mapper = new PhoenixPhotoJsonMapper();
        $this->assertNull($mapper->mapPhotoRow(['id' => 1]));
    }

    public function testTakenAtParsesIso8601(): void
    {
        $mapper = new PhoenixPhotoJsonMapper();
        $item = $mapper->mapPhotoRow([
            'id' => 1,
            'photo_url' => 'https://x.test/p.jpg',
            'taken_at' => '2024-06-15T06:30:00Z',
        ]);

        self::assertNotNull($item);
        $this->assertSame('2024-06-15', $item->getTakenAt()?->format('Y-m-d'));
    }
}
