<?php

declare(strict_types=1);

namespace App\Integration\Phoenix\Dto;

final readonly class PhoenixPhotoData
{
    public function __construct(
        private int $id,
        private string $photoUrl,
        private ?string $camera,
        private ?string $description,
        private ?string $location,
        private ?\DateTimeImmutable $takenAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    public function getCamera(): ?string
    {
        return $this->camera;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getTakenAt(): ?\DateTimeImmutable
    {
        return $this->takenAt;
    }
}
