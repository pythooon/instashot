<?php

declare(strict_types=1);

namespace App\Integration\Phoenix\Mapper;

use App\Integration\Phoenix\Dto\PhoenixPhotoData;

final readonly class PhoenixPhotoJsonMapper
{
    /**
     * @param array<string, mixed> $payload
     *
     * @return list<PhoenixPhotoData>
     */
    public function mapPhotosPayload(array $payload): array
    {
        $rows = $payload['photos'] ?? null;
        if (!\is_array($rows)) {
            return [];
        }

        /** @var list<PhoenixPhotoData> $out */
        $out = [];
        foreach ($rows as $row) {
            if (!\is_array($row)) {
                continue;
            }

            $item = $this->mapPhotoRow($row);
            if ($item !== null) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapPhotoRow(array $row): ?PhoenixPhotoData
    {
        $id = $row['id'] ?? null;
        $photoUrl = $row['photo_url'] ?? null;
        if (!\is_int($id) && !\is_numeric($id)) {
            return null;
        }

        if (!\is_string($photoUrl) || $photoUrl === '') {
            return null;
        }

        $camera = \is_string($row['camera'] ?? null) ? $row['camera'] : null;
        $description = \is_string($row['description'] ?? null) ? $row['description'] : null;
        $location = \is_string($row['location'] ?? null) ? $row['location'] : null;

        return new PhoenixPhotoData(
            id: (int) $id,
            photoUrl: $photoUrl,
            camera: $camera,
            description: $description,
            location: $location,
            takenAt: $this->parseTakenAt($row['taken_at'] ?? null),
        );
    }

    private function parseTakenAt(mixed $value): ?\DateTimeImmutable
    {
        if (!\is_string($value) || $value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }
}
